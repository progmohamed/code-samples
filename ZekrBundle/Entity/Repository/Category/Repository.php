<?php

namespace ZekrBundle\Entity\Repository\Category;

use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\Category;


class Repository extends EntityRepository
{

    private $builder;
    private $frontData;

    public function getBuilder()
    {
        if (!$this->builder) {
            $this->builder = new Builder($this->getEntityManager());
        }
        return $this->builder;
    }

    public function getFrontData()
    {
        if (!$this->frontData) {
            $this->frontData = new FrontData($this->getEntityManager());
        }
        return $this->frontData;
    }

    public function getCategoryTreeWithRoot()
    {
        return [(object)[
            'key' => 0,
            'title' => 'الجذر',
            'folder' => true,
            'expanded' => true,
            'hideCheckbox' => true,
            'children' => $this->getCategoryTree()
        ]];
    }

    public function getCategoryTree($parent = null)
    {
        $em = $this->getEntityManager();
        $params = [];
        $dql = "SELECT c
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL ";

        if (is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        } else {
            $dql .= "AND c.parent = :parent ";
            $params['parent'] = $parent;
        }
        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);
        if (count($params)) {
            $query->setParameters($params);
        }
        $result = $query->getResult();

        $return = array();
        foreach ($result as $category) {
            $children = $this->getCategoryTree($category->getId());
            $hasChildren = sizeof($children) ? true : false;
            $obj = [
                'key' => $category->getId(),
                'title' => $category->translate()->getName()
            ];

            $obj['icon'] = 'folder1.gif';
            $obj['hideCheckbox'] = false;

            if ($hasChildren) {
                $obj['expanded'] = false;
                $obj['children'] = $children;
            } else {
                $obj['children'] = [];
            }
            $return[] = (object)$obj;
        }
        return $return;
    }


    public function getCategoryFullPath(Category $category)
    {
        $path = [];
        $path[] = $category->translate()->getName();
        while ($category->getParent()) {
            $category = $category->getParent();
            $path[] = $category->translate()->getName();
        }
        $path = array_reverse($path);
        return implode(" / ", $path);
    }


    public function getApiCategories($parent = null, $locale = null)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT c, ct
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL 
        AND c.active = 1 ";

        if (is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        } else {
            $dql .= "AND c.parent = :parent ";
            $parameters['parent'] = $parent;
        }

        if (!empty($locale)) {
            $dql .= "AND ct.locale = :locale ";
            $parameters['locale'] = $locale;
        }

        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $result = $query->getResult();

        $return = array();
        foreach ($result as $category) {
            $children = $this->getApiCategories($category->getId(), $locale);
            $return[] = (object)$category;
        }

        return $return;
    }


    public function getCategories($parent = null)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT c, ct
        FROM ZekrBundle:Category c
        LEFT JOIN c.translations ct
        WHERE c.deletedAt is NULL 
        AND c.active = 1 ";

        if (is_null($parent)) {
            $dql .= "AND c.parent IS NULL ";
        } else {
            $dql .= "AND c.parent = :parent ";
            $parameters['parent'] = $parent;
        }

        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $result = $query->getResult();

        $return = array();
        foreach ($result as $category) {
//            $children = $this->getCategories($category->getId());
            $return[] = (object)$category;
        }

        return $return;
    }

    public function getCategoriesCount()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT COUNT(c.id)
        FROM ZekrBundle:Category c
        WHERE c.deletedAt is NULL 
        AND c.active = 1 ";

        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        return $result = $query->getSingleScalarResult();
    }

}
