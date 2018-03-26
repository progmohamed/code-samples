<?php

namespace ZekrBundle\Entity\Repository\SelectedVideo;

use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\SelectedVideo;

class SelectedVideoRepository extends EntityRepository
{

    private $frontData;

    public function getFrontData()
    {
        if (!$this->frontData) {
            $this->frontData = new FrontData($this->getEntityManager());
        }
        return $this->frontData;
    }

    public function getGrid()
    {
        $em = $this->getEntityManager();

        $dql = "SELECT s AS data
        FROM ZekrBundle:SelectedVideo s
        INNER JOIN s.video v
        WHERE v.deletedAt is NULL
        ORDER BY s.sortOrder";

        $query = $em->createQuery($dql);

        $result = $query->getResult();
        $return = array();
        $count = count($result);
        $counter = 0;
        foreach($result as $row) {
            $counter ++;
            $row['up'] = true;
            $row['down'] = true;
            if($counter == 1) {
                $row['up'] = false;
            }
            if($count == $counter) {
                $row['down'] = false;
            }
            $return[] = $row;
        }
        return $return;
    }

    public function getMaxOrder()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT MAX(s.sortOrder)
        FROM ZekrBundle:SelectedVideo s";
        $query = $em->createQuery($dql);
        $max = $query->getSingleScalarResult();
        return $max;
    }


    public function moveUp(SelectedVideo $entity)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s
        FROM ZekrBundle:SelectedVideo s
        WHERE s.sortOrder < :currentOrder ";
        $params['currentOrder'] = $entity->getSortorder();
        $dql .= "ORDER BY s.sortOrder DESC ";
        $query = $em->createQuery( $dql );
        $query->setParameters( $params );
        try {
            $entityPrevios = $query->setMaxResults(1)->getSingleResult();
            $temp = $entityPrevios->getSortorder();
            $entityPrevios->setSortorder( $entity->getSortorder() );
            $em->flush();
            $entity->setSortorder( $temp );
            $em->flush();
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function moveDown(SelectedVideo $entity)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s
        FROM ZekrBundle:SelectedVideo s
        WHERE s.sortOrder > :currentOrder ";
        $params['currentOrder'] = $entity->getSortorder();
        $dql .= "ORDER BY s.sortOrder ";
        $query = $em->createQuery( $dql );
        $query->setParameters( $params );

        try {
            $entityNext = $query->setMaxResults(1)->getSingleResult();
            $temp = $entityNext->getSortorder();
            $entityNext->setSortorder( $entity->getSortorder() );
            $em->flush();
            $entity->setSortorder( $temp );
            $em->flush();
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function resort(array $newSort)
    {
        $em = $this->getEntityManager();

        foreach ($newSort as $index => $video) {
            $rb = $em->getRepository('ZekrBundle:SelectedVideo')->findOneBy(array('id'=>$video));
            $rb->setSortorder($index+1);
            try {
                $em->flush();
            } catch (\Exception $e) {

            }
        }

    }

}
