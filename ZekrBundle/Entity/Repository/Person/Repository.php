<?php

namespace ZekrBundle\Entity\Repository\Person;

use Doctrine\ORM\EntityRepository;

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

    public function getPersons($paginator, $page, $limit, $sortField, $sortDirection)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p
        FROM ZekrBundle:Person p
        INNER JOIN p.translations pt
        WHERE p.active = true AND p.deletedAt IS NULL 
        ";

        if ($sortField == 'num_active_topics') {
            $dql .= ' ORDER BY p.numActiveTopics '.$sortDirection;
        }elseif ($sortField == 'name') {
            $dql .= ' ORDER By  pt.name '.$sortDirection ;
        } else {
            $dql .= ' ORDER By p.id '.$sortDirection;
        }
        $query = $em->createQuery($dql);

        return $paginator->paginate($query, $page, $limit);
    }

}