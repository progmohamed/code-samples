<?php

namespace ZekrBundle\Entity\Repository\ApiProject;

use Doctrine\ORM\EntityRepository;

class Repository extends EntityRepository
{

    private $dataGrid;

    public function getDataGrid()
    {
        if(!$this->dataGrid) {
            $this->dataGrid = new DataGrid($this->getEntityManager());
        }
        return $this->dataGrid;
    }

}