<?php

namespace SearchBundle\Service\Search;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Search
{
    private $container;
    private $indexer;
    private $paginator;
    private $pagination;
    private $pageSize = 50;
    private $client;
    private $select;
    private $query;
    private $queryDefaultField;
    private $checkAllFacetsByDefault = false;
    private $facetFilterSelectedValues = [];
    private $facetFields = [];
    private $filterQuery = [];
    private $sort = [];
    private $facetFilterQuery = [];
    private $facetSet = [];
    private $currentPage;
    private $helper;
    private $moreResults = false;

    function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->setClient( $this->getContainer()->get('solarium.client.default') );
        $this->setPaginator( $this->getContainer()->get('knp_paginator') );
    }

    public function getIndexer($indexer)
    {
        if(!$this->indexer) {
            $this->indexer = $indexer;
//            $this->indexer = new Indexer($this->getContainer());
        }
        return $this->indexer;
    }

    public function setClientName($clientName)
    {
        $this->setClient( $this->getContainer()->get($clientName) );
        return $this;
    }


    public function getContainer()
    {
        return $this->container;
    }

    public function clearAll()
    {
        $this->queryDefaultField = null;
        $this->checkAllFacetsByDefault = false;
        $this->facetFilterSelectedValues = [];
        $this->facetFields = [];
        $this->filterQuery = [];
        $this->sort = [];
        $this->facetFilterQuery = [];
        $this->facetSet = [];
        $this->currentPage = null;
        $this->moreResults = false;
    }

    public function hasMoreResults()
    {
        return $this->moreResults;
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

    public function getQueryDefaultField()
    {
        return $this->queryDefaultField;
    }

    public function setQueryDefaultField($queryDefaultField)
    {
        $this->queryDefaultField = $queryDefaultField;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    public function getSelect()
    {
        $this->select = $this->getClient()->createSelect();
        return $this->select;
    }

    public function getHelper()
    {
        if(!$this->helper) {
            $query = $this->getSelect();
            $helper = $query->getHelper();
            $this->helper = $helper;
        }
        return $this->helper;
    }


    public function getPaginator()
    {
        return $this->paginator;
    }

    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    private function setPagination($pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    public function addFacetField($field)
    {
        $this->facetFields[$field] = $field;
        return $this;
    }

    public function removeFacetField($field)
    {
        if(isset($this->facetFields[$field])) {
            unset($this->facetFields[$field]);
        }
        return $this;
    }

    public function getFacetFields()
    {
        return $this->facetFields;
    }

    public function clearFacetFields()
    {
        $this->facetFields = [];
        return $this;
    }

    public function addFilterQuery($name, $value)
    {
        if(array_key_exists($name, $this->filterQuery)) {
            if(!in_array($value, $this->filterQuery[$name])) {
                $this->filterQuery[$name][] = $value;
            }
        }else {
            $this->filterQuery[$name][] = $value;
        }
        return $this;
    }

    public function removeFilterQuery($name)
    {
        unset($this->filterQuery[$name]);
        return $this;
    }

    public function getFilterQuery()
    {
        return $this->filterQuery;
    }

    public function clearFilterQuery()
    {
        $this->filterQuery = [];
        return $this;
    }

    public function addSort($sort, $direction = 'asc')
    {
        $direction = strtolower($direction);
        if(!in_array($direction, ['asc', 'desc'])) {
            throw new \Exception('direction parameter must be either "asc" or "desc" ');
        }
        $this->sort[] = [$sort, $direction];
        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function addFacetFilterQuery($name, $value)
    {
        $this->facetFilterQuery[$name] = $value;
        return $this;
    }

    public function removeFacetFilterQuery($name)
    {
        unset($this->facetFilterQuery[$name]);
        return $this;
    }

    public function getFacetFilterQuery()
    {
        return $this->facetFilterQuery;
    }

    public function clearFacetFilterQuery()
    {
        $this->facetFilterQuery = [];
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function setFacetFilterValues($field, array $selectedValues)
    {
        $this->facetFilterSelectedValues[$field] = $selectedValues;
        $this->removeFacetFilterQuery($field);
        if(is_array($selectedValues) && count($selectedValues)) {
            $queryValues = [];
            foreach($selectedValues as $value) {
                $queryValues[] = "\"".addslashes($value)."\"";
            }
            $queryValue = $field.': ('.implode(' OR ', $queryValues).')';
            $this->addFacetFilterQuery($field, $queryValue);
        }
        return $this;
    }


    public function getFacetFilterSelectedValues($field)
    {
        if(isset($this->facetFilterSelectedValues[$field])) {
            return $this->facetFilterSelectedValues[$field];
        }
        return null;
    }

    private function setFacet($facetField, $valuesArray)
    {
        $this->facetSet[$facetField] = $valuesArray;
        return $this;
    }

    public function getFacetSet()
    {
        return $this->facetSet;
    }

    public function getFacet($facetField)
    {
        if(isset($this->facetSet[$facetField])) {
            return $this->facetSet[$facetField];
        }
        return null;
    }

    public function checkAllFacetsByDefault($value)
    {
        $this->checkAllFacetsByDefault = $value;
    }

    private function getBindedFacetArray($facetArray, $selectedValues)
    {
        $return = [];
        if(!is_array($selectedValues)) {
            $selectedValues = [];
        }
        if($facetArray) {
            foreach ($facetArray as $name => $count) {
                if($count) {
                    if(is_null($selectedValues) || $this->checkAllFacetsByDefault) {
                        $checked = true;
                    }else{
                        $checked = in_array($name, $selectedValues) ? true : false;
                    }
                    $return[] = [
                        'name'    => $name,
                        'count'   => $count,
                        'checked' => $checked,
                    ];
                }
            }
        }
        return $return;
    }

    public function getResults()
    {
        $select = $this->getSelect();
        $select->setOmitHeader(false);
        $helper = $select->getHelper();
        //$arUtil = $this->getContainer()->get('arabic_utilities');

        if(count($this->getFacetFields())) {
            $facetSet = $select->getFacetSet();
            foreach($this->getFacetFields() as $field) {
                $facetSet->createFacetField($field)->setField($field);
            }
        }


        if ($this->getQuery()) {
            //$select->setQuery('question_n:'.$helper->escapeTerm($this->getQuery()));
            //$select->setQuery($helper->escapeTerm($this->getQuery()));
            $select->setQuery($this->getQuery());
            $select->setQuerydefaultfield($this->getQueryDefaultField());
            //$select->createFilterQuery('text')->setQuery('{!lucene}text:' . $helper->escapeTerm($arUtil->normalizeSimple($this->getQuery())));
        }

        if( count($this->getFilterQuery()) ) {
            foreach($this->getFilterQuery() as $key => $value) {
                //$select->createFilterQuery($key)->setQuery( $arUtil->normalizeSimple($value) );
                $queryValue = implode(' OR ', $value);
                $select->createFilterQuery($key)->setQuery( $queryValue );
            }
        }

        if(count($this->getSort())){
            foreach ($this->getSort() as $sort) {
                $select->addSort($sort[0], $sort[0] == 'asc' ? $select::SORT_ASC : $select::SORT_DESC);
            }
        }

        $tempResults = $this->getClient()->select($select);

        //save facet arrays
        foreach($this->getFacetFields() as $facetField) {
            $bindedFacetArray = $this->getBindedFacetArray(
                $tempResults->getFacetSet()->getFacet($facetField),
                $this->getFacetFilterSelectedValues($facetField)
            );
            $this->setFacet($facetField, $bindedFacetArray);
        }

        if( count($this->getFacetFilterQuery()) ) {
            foreach($this->getFacetFilterQuery() as $key => $value) {
                $select->createFilterQuery($key)->setQuery($value);
            }
        }

        $pagination = $this->getPaginator()->paginate(
            [$this->getClient(), $select],
            $this->getCurrentPage(),
            $this->getPageSize()
        );
        //$pagination ->setTemplate(':front:pagination.html.twig');
        $this->setPagination($pagination);

        $results = $this->getPagination()->getCustomParameter('result');

        if($results->getNumFound() == 0) {
            $this->moreResults = false;
        }else {
            $lastPage = ceil($results->getNumFound() / $this->getPageSize());
            $this->moreResults = ($this->getCurrentPage() == $lastPage) ? false : true;
        }
        return $results;
    }
}