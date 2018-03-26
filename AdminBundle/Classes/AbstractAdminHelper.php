<?php

namespace AdminBundle\Classes;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractAdminHelper implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    function __construct($container)
    {
        $this->setContainer($container);
    }

    public function getUserRestrictions($user)
    {
        return 0;
    }
}