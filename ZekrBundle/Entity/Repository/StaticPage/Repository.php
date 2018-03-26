<?php

namespace ZekrBundle\Entity\Repository\StaticPage;

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

}