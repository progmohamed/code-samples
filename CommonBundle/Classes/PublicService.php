<?php

namespace CommonBundle\Classes;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class PublicService implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    protected $relatedServices = [];

    abstract public function getName();

    public function addRelatedService($service)
    {
        $this->relatedServices[] = $service;
        return $this;
    }

    public function getRelatedServices()
    {
        return $this->relatedServices;
    }

}