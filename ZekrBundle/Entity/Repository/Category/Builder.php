<?php

namespace ZekrBundle\Entity\Repository\Category;

use ZekrBundle\Entity\Category;
use ZekrBundle\Entity\CategoryDescendant;

class Builder
{

    private $em;

    function __construct($em)
    {
        $this->em = $em;
    }

    public function add(Category $category, Category $parent = null)
    {
        $category->setParent($parent);
        $max = $this->getMaxOrder($parent);
        $category->setSortOrder($max + 1);
        $this->em->persist($category);
        $this->em->flush();
        $this->reCalculateCounters();
    }

    public function edit(Category $category)
    {
        $this->em->flush($category);
        $this->reCalculateCounters();
    }

    public function remove(Category $category)
    {
        try {
            $ancestors = $this->getCategoryAncestors($category, true);
            $this->em->remove($category);
            $this->em->flush();
            $this->reCalculateCounters();
            foreach($ancestors as $ancestor) {
                $this->updateCategoryVideoCounters($ancestor);
            }
        }catch (\Exception $e) {
            throw new \Exception('خطأ في حذف التصنيف... ربما يكون مستخدما في فتاوى');
        }
    }
    
    public function updateCategoriesVideoCounters($categories)
    {
        $ids = [];
        foreach($categories as $category) {
            $ancestors = $this->getCategoryAncestors($category);
            $ids = array_merge($ids, $ancestors);
        }
        $ids = array_unique($ids);

        $dql = "SELECT c
        FROM ZekrBundle:Category c
        WHERE c.deletedAt is NULL
        AND c.id IN(:ids) ";
        $query = $this->em->createQuery($dql);
        $query->setParameter('ids', $ids);
        $result = $query->getResult();
        foreach($result as $category) {
            $numDirectTopics = $this->getCategoryNumDirectVideos($category);
            $category->setNumDirectTopics( $numDirectTopics );

            $numAllTopics = $this->getCategoryNumAllVideos($category);
            $category->setNumAllTopics( $numAllTopics + $numDirectTopics );

            $numDirectActiveTopics = $this->getCategoryNumDirectVideos($category, true);
            $category->setNumDirectActiveTopics( $numDirectActiveTopics );

            $numAllActiveTopics = $this->getCategoryNumAllVideos($category, true);
            $category->setNumAllActiveTopics( $numDirectActiveTopics + $numAllActiveTopics);
        }
        $this->em->flush();
    }

    private function updateCategoryVideoCounters($category)
    {
        if($category->getId()) {
            $numDirectTopics = $this->getCategoryNumDirectVideos($category);
            $category->setNumDirectTopics($numDirectTopics);

            $numAllTopics = $this->getCategoryNumAllVideos($category);
            $category->setNumAllTopics($numAllTopics + $numDirectTopics);

            $numDirectActiveTopics = $this->getCategoryNumDirectVideos($category, true);
            $category->setNumDirectActiveTopics($numDirectActiveTopics);

            $numAllActiveTopics = $this->getCategoryNumAllVideos($category, true);
            $category->setNumAllActiveTopics($numDirectActiveTopics + $numAllActiveTopics);

            $this->em->flush();
        }
    }

    private function getCategoryNumDirectVideos(Category $category, $activeOnly = false)
    {
        $dql = "SELECT COUNT(v.id)
        FROM ZekrBundle:Video v
        INNER JOIN v.category c
        WHERE v.deletedAt is NULL
        AND c.deletedAt is NULL
        AND c.id = :category ";
        if($activeOnly) {
            $dql.= "AND v.active = true ";
        }
        $query = $this->em->createQuery($dql);
        $query->setParameter('category', $category);
        $max = $query->getSingleScalarResult();
        return intval($max);
    }

    private function getCategoryNumAllVideos(Category $category, $activeOnly = false)
    {
        $subTree = $this->getNodeSubTree($category);
        $counter = 0;
        foreach($subTree as $element) {
            $counter += $this->getCategoryNumDirectVideos($element['category'], $activeOnly);
        }
        return $counter;
    }

    private function getCategoryAncestors(Category $category, $returnEntities = false)
    {
        $out = [ $returnEntities ? $category : $category->getId()];
        while($category->getParent()) {
            $category = $category->getParent();
            $out[] = $returnEntities ? $category : $category->getId();
        }
        return $out;
    }

    private function reCalculateCounters()
    {
        $dql = "SELECT c
        FROM ZekrBundle:Category c 
        WHERE c.deletedAt is NULL ";
        $query = $this->em->createQuery($dql);
        $result = $query->getResult();
        foreach($result as $category) {
            $values = $this->getCategoryCounters($category);
            $category->setNumDirectChildren($values['num_direct_children']);
            $category->setNumAllChildren($values['num_all_children']);
            $category->setNumDirectTopics($values['num_direct_topics']);
            $category->setNumAllTopics($values['num_all_topics']);
            $this->updateTreeDescendants($category);
        }
        $this->em->flush();
    }

    private function getMaxOrder($parent)
    {
        $params = [];
        $dql = "SELECT MAX(c.sortOrder)
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL ";
        if(!$parent) {
            $dql .= "AND c.parent IS NULL ";
        }else{
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $parent;
        }
        $query = $this->em->createQuery($dql);
        $query->setParameters($params);
        $max = $query->getSingleScalarResult();
        return intval($max);
    }

    private function getDirectChildren($parent)
    {
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL ";
        if(is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        }else{
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $parent;
        }
        $dql .= "ORDER BY c.sortOrder";
        $query = $this->em->createQuery($dql);
        if(count($params)) {
            $query->setParameters($params);
        }
        return $query->getResult();
    }

    private function getCategoryCounters(Category $parent = null)
    {
        $values = [
            'num_direct_children' => 0,
            'num_all_children' => 0,
            'num_direct_topics' => 0,
            'num_all_topics' => 0
        ];
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        WHERE c.deletedAt is NULL ";
        if(is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        }else{
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $parent;
        }
        $dql .= "ORDER BY c.sortOrder ";
        $query = $this->em->createQuery($dql);
        if(count($params)) {
            $query->setParameters($params);
        }
        $result = $query->getResult();
        foreach($result as $category) {
            $values['num_direct_children'] ++;
            $values['num_all_children'] ++;
            $childValues = $this->getCategoryCounters($category);
            $values['num_all_children'] += $childValues['num_all_children'];
            $values['num_all_topics'] += $childValues['num_all_topics'];
        }
        return $values;
    }

    private function updateTreeDescendants(Category $category)
    {
        $subTree = $this->getNodeSubTree($category);
        $subTree = array_merge([
            ['category' => $category, 'active' => $category->getActive()]
        ], $subTree);
        $nodes = [];
        foreach($subTree as $element) {
            $nodes[] = $element['category'];
        }

        $queryBuilder = $this->em->createQueryBuilder()
            ->delete('ZekrBundle:CategoryDescendant', 'cd')
            ->where('cd.node = :category')
            ->andWhere('cd.descendant NOT IN(:descendants)')
            ->setParameter('category', $category)
            ->setParameter('descendants', $nodes);
        $queryBuilder->getQuery()->execute();

        $query = $this->em->createQuery("SELECT cd FROM ZekrBundle:CategoryDescendant cd WHERE cd.node = :category ");
        $query->setParameter('category', $category);
        $rest = $query->getResult();
        foreach($subTree as $element) {
            $found = false;
            foreach($rest as $categoryDescendant) {
                if($element['category']->getId() == $categoryDescendant->getDescendant()->getId()) {
                    $found = true;
                    $categoryDescendant->setActive($element['active']);
                }
            }
            if(!$found) {
                $categoryDescendant = new CategoryDescendant();
                $categoryDescendant->setNode($category);
                $categoryDescendant->setDescendant($element['category']);
                $categoryDescendant->setActive($element['active']);
                $this->em->persist($categoryDescendant);
            }
        }
        $this->em->flush();
    }

    private function getNodeSubTree(Category $parent)
    {
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        WHERE c.parent = :parent  
		AND c.deletedAt is NULL
        ORDER BY c.sortOrder ";
        $query = $this->em->createQuery($dql);
        $query->setParameters(['parent' => $parent]);
        $result = $query->getResult();
        $out = [];
        foreach($result as $category) {
            $out[] = [
                'category' => $category,
                'active' => $parent->getActive() ? $category->getActive() : false,
            ];
            $childValues = $this->getNodeSubTree($category);
            if(!$category->getActive() || !$parent->getActive()) {
                foreach($childValues as $k => $v) {
                    $childValues[$k]['active'] = false;
                }
            }
            $out = array_merge($out, $childValues);
        }
        return $out;
    }

    public function move(Category $entityMove, Category $entityTo = null, $mode)
    {
        if('over' == $mode || 'child' == $mode) {
            $max = $this->getMaxOrder($entityTo);
            $entityMove->setParent($entityTo);
            $entityMove->setSortOrder($max + 1);
        }else{
            if(!$entityTo) {
                throw new \Exception('Category not found');
            }
            if('firstChild' == $mode) {
                $i = 2;
                $children = $entityTo->getChildren();
                foreach($children as $child) {
                    $child->setSortOrder($i);
                    $i++;
                }
                $entityMove->setParent($entityTo);
                $entityMove->setSortOrder(1);
            }
            if('before' == $mode) {
                $entityMove->setParent($entityTo->getParent());
                $siblings = $this->getDirectChildren($entityTo->getParent());
                $i = 1;
                foreach($siblings as $sibling) {
                    if($sibling->getId() != $entityMove->getId()) {
                        $sibling->setSortOrder($i);
                    }
                    if($sibling->getId() == $entityTo->getId()) {
                        $entityMove->setSortOrder($i);
                        $i++;
                        $entityTo->setSortOrder($i);
                    }
                    $i++;
                }
            }
            if('after' == $mode) {
                $entityMove->setParent($entityTo->getParent());
                $siblings = $this->getDirectChildren($entityTo->getParent());
                $i = 1;
                foreach($siblings as $sibling) {
                    if($sibling->getId() != $entityMove->getId()) {
                        $sibling->setSortOrder($i);
                    }
                    if($sibling->getId() == $entityTo->getId()) {
                        $entityTo->setSortOrder($i);
                        $i++;
                        $entityMove->setSortOrder($i);
                    }
                    $i++;
                }
            }
        }
        $this->em->flush();
        $this->reCalculateCounters();
        $this->updateCategoryVideoCounters($entityMove);
        $this->updateCategoryVideoCounters($entityTo);
    }

    public function moveUp(Category $category)
    {
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.sortOrder < :currentOrder 
        AND c.deletedAt is NULL ";
        $params['currentOrder'] = $category->getSortOrder();

        if(is_null($category->getParent())) {
            $dql .= "AND c.parent IS NULL ";
        }else{
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $category->getParent();
        }
        $dql .= "ORDER BY c.sortOrder DESC";

        $query = $this->em->createQuery( $dql );
        $query->setParameters( $params );
        try {
            $entityPreviosTemp = $query->setMaxResults(1)->getSingleResult();
            if($entityPreviosTemp) {
                $categoryRepository = $this->em->getRepository('ZekrBundle:Category');
                $entityPrevios = $categoryRepository->find($entityPreviosTemp->getId());
                $temp = $entityPrevios->getSortOrder();
                $entityPrevios->setSortOrder( $category->getSortOrder() );
                $this->em->flush();
                $category->setSortOrder( $temp );
                $this->em->flush();
                $this->reCalculateCounters();
                $this->updateCategoryVideoCounters($category);
                $this->updateCategoryVideoCounters($entityPreviosTemp);
            }
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function moveDown(Category $category)
    {
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.sortOrder > :currentOrder 
        AND c.deletedAt is NULL ";
        $params['currentOrder'] = $category->getSortOrder();
        if(!$category->getParent()) {
            $dql .= "AND c.parent IS NULL ";
        }else{
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $category->getParent();
        }
        $dql .= "ORDER BY c.sortOrder";

        $query = $this->em->createQuery( $dql );
        $query->setParameters( $params );

        try {
            $entityNextTemp = $query->setMaxResults(1)->getSingleResult();
            if($entityNextTemp) {
                $categoryRepository = $this->em->getRepository('ZekrBundle:Category');
                $entityNext = $categoryRepository->find($entityNextTemp->getId());
                $temp = $entityNext->getSortOrder();
                $entityNext->setSortOrder( $category->getSortOrder() );
                $this->em->flush();
                $category->setSortOrder( $temp );
                $this->em->flush();
                $this->reCalculateCounters();
                $this->updateCategoryVideoCounters($category);
                $this->updateCategoryVideoCounters($entityNextTemp);
            }
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }

}