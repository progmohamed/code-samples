<?php

namespace ConfigBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use AdminBundle\Classes\AbstractDataFixturesService;

class DataFixturesService extends AbstractDataFixturesService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    function __construct()
    {
        $this->addDataFixture(new \ConfigBundle\DataFixtures\ORM\LoadMenu());
        $this->addDataFixture(new \ConfigBundle\DataFixtures\ORM\LoadConfigData());
    }
}