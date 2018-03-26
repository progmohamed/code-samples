<?php

namespace ZekrBundle\Entity\Repository\VideoType;

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


    public function getVideoType($paginator, $page, $limit, $sortField, $sortDirection)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT vt
        FROM ZekrBundle:VideoType vt
        INNER JOIN vt.translations vtt
        WHERE vt.active = true
        ";

        if ($sortField == 'num_active_topics') {
            $dql .= ' ORDER BY vt.numActiveTopics '.$sortDirection;
        }elseif ($sortField == 'name') {
            $dql .= ' ORDER By  vtt.name '.$sortDirection ;
        } else {
            $dql .= ' ORDER By vt.id '.$sortDirection;
        }
        $query = $em->createQuery($dql);

        return $paginator->paginate($query, $page, $limit);
    }

}