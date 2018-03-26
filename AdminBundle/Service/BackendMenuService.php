<?php

namespace AdminBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BackendMenuService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $services = [];

    public function addService($service)
    {
        $this->services[] = $service;
    }

    public function getServices()
    {
        return $this->services;
    }

}