<?php

namespace CommonBundle\Service;

class GridPaginator
{
    private $container;
    private $knpPaginator;
    private $pageSize;
    private $query;
    private $countQuery;
    private $currentPage;
    private $handleLastPageScenario = false;

    public function __construct($container = null)
    {
        $this->container = $container;
        $this->knpPaginator = $this->getContainer()->get('knp_paginator');
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    public function setQuery(\Doctrine\ORM\Query $query)
    {
        $this->query = $query;
        return $this;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    function getHandleLastPageScenario()
    {
        return $this->handleLastPageScenario;
    }

    function setHandleLastPageScenario($handleLastPageScenario)
    {
        $this->handleLastPageScenario = $handleLastPageScenario;
        return $this;
    }

        
    public function setCountQuery(\Doctrine\ORM\Query $countQuery)
    {
        $this->countQuery = $countQuery;
        return $this;
    }
    
    public function getCountQuery()
    {
        return $this->countQuery;
    }
    
    public function getPageSize()
    {
        return $this->pageSize;
    }
    
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }
    
    public function setCurrentPage($currentPage)
    {
        $currentPage = intval($currentPage);
        if($currentPage < 1) {
            $currentPage = 1;
        }
        $this->currentPage = $currentPage;
        return $this;
    }
    
    public function getCurrentPage()
    {
        return $this->currentPage ? $this->currentPage : 1;
    }

    private function cloneQuery(\Doctrine\ORM\Query $query)
    {
        $parameters = [];
        foreach($query->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }
        $clone = clone $query;
        $clone->setParameters($parameters);
        return $clone;
    }
    
    public function getPagination()
    {
        $query = $this->getQuery();

        $countQuery = $this->getCountQuery();
        if( $countQuery ) {
            $totalCount = $countQuery->getSingleScalarResult();
        }
        
        if( $this->getHandleLastPageScenario() ) {
            $queryCheck = $this->cloneQuery($query);
            if(isset($totalCount)) {
                $queryCheck->setHint('knp_paginator.count', $totalCount);
            }

            $paginationCheck = $this->knpPaginator->paginate($queryCheck, 1);
            $paginationData = $paginationCheck->getPaginationData();
            if($this->getCurrentPage() > $paginationData['pageCount']) {
                $this->setCurrentPage($paginationData['pageCount']);
            }
        }
        
        if(isset($totalCount)) {
            $query->setHint('knp_paginator.count', $totalCount);
        }
        
        if($this->getPageSize() ) {
            $pagination = $this->knpPaginator->paginate(
                $query,
                $this->getCurrentPage(), 
                $this->getPageSize() 
            );
        }else{
            $pagination = $this->knpPaginator->paginate(
                $query, 
                $this->getCurrentPage()
            );
        }
        return $pagination;
    }

}
