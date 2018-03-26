<?php

namespace ZekrBundle\Service\Search\Indexer;

abstract class IndexerAbstract
{
    protected $container;
    protected $client;
    protected $update;
    protected $autoCommit = true;

    public function __construct($container)
    {
        $this->setContainer($container);
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

    public function setClientName($clientName)
    {
        $this->setClient( $this->getContainer()->get($clientName) );
        return $this;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }
    
    public function setAutoCommit($autoCommit)
    {
        $this->autoCommit = $autoCommit;
        return $this;
    }
    
    public function getAutoCommit()
    {
        return $this->autoCommit;
    }
    
    protected function getUpdate()
    {
        /* Singleton pattern */
        if( !$this->update ) {
            $this->update = $this->client->createUpdate();
        }
        return $this->update;
    }
    
    public function indexDocument($document)
    {
        if(is_array($document)) {
            $docs = $document;
        }else{
            $docs = [$document];
        }
        $update = $this->getUpdate();
        $update->addDocuments($docs, true);
        $update->addCommit();
        if( $this->getAutoCommit() ) {
            $this->commit();
        }
    }
    
    public function commit()
    {
        if($this->update) {
            $return = $this->client->update($this->update);
            /* I set update to null after each commit to avoid memory leak */
            $this->update = null;
            return $return;
        }
    }
    
    public function optimize()
    {
        $update = $this->getUpdate();
        $update->addOptimize();
        $this->commit();
    }
    
    public function deleteDocument($id)
    {
        $update = $this->getUpdate();
        $update->addDeleteById($id);
        $update->addCommit();
        if( $this->getAutoCommit() ) {
            return $this->commit();
        }
    }
    
    public function deleteDocumentsByField($field, $value)
    {
        //Example: delete all documents belong to "auth:23"
        //Example: delete all articles "doc_id:article-*"
        $update = $this->getUpdate();
        $update->addDeleteQuery("{$field}:{$value}");
        $update->addCommit();
        if( $this->getAutoCommit() ) {
            return $this->commit();
        }
    }
    
    public function purge()
    {
        $update = $this->getUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        if( $this->getAutoCommit() ) {
            return $this->commit();
        }
    }
}
