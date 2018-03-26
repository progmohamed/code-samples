<?php

namespace ZekrBundle\Entity\Repository\Rewaya;

use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\Rewaya;

class Repository extends EntityRepository
{

    private $dataGrid;
    private $frontData;

    public function getDataGrid()
    {
        if (!$this->dataGrid) {
            $this->dataGrid = new DataGrid($this->getEntityManager());
        }
        return $this->dataGrid;
    }

    public function getFrontData()
    {
        if (!$this->frontData) {
            $this->frontData = new FrontData($this->getEntityManager());
        }
        return $this->frontData;
    }

    public function getMaxOrder()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT MAX(r.sortOrder)
        FROM ZekrBundle:Rewaya r";
        $query = $em->createQuery($dql);
        $max = $query->getSingleScalarResult();
        return $max;
    }

    public function resort(array $newSort)
    {
        $em = $this->getEntityManager();

        foreach ($newSort as $index => $rewaya) {
            $rb = $em->getRepository('ZekrBundle:Rewaya')->findOneBy(array('id' => $rewaya));
            $rb->setSortorder($index + 1);
            try {
                $em->flush();
            } catch (\Exception $e) {

            }
        }

    }


    public function getRewaya($paginator, $page, $limit)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT r
        FROM ZekrBundle:Rewaya r
        WHERE r.active = true
        ";
        $query = $em->createQuery($dql);

        return $paginator->paginate($query, $page, $limit);
    }

}