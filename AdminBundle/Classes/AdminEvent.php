<?php

namespace AdminBundle\Classes;


use Symfony\Component\EventDispatcher\GenericEvent;

class AdminEvent extends GenericEvent
{
    protected $cascadeMessages;
    protected $restrictMessages;



    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }


    public function getCascadeMessages()
    {
        return $this->cascadeMessages;
    }


    public function addCascadeMessages($cascadeMessages)
    {
        $this->cascadeMessages[] = $cascadeMessages;

        return $this;
    }

    public function getRestrictMessages()
    {
        return $this->restrictMessages;
    }


    public function addRestrictMessages($restrictMessages)
    {
        $this->restrictMessages[] = $restrictMessages;

        return $this;
    }

}
