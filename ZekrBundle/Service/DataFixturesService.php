<?php

namespace ZekrBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use AdminBundle\Classes\AbstractDataFixturesService;

class DataFixturesService extends AbstractDataFixturesService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    function __construct()
    {
        $this->addDataFixture(new \ZekrBundle\DataFixtures\ORM\LoadMenu());
        $this->addDataFixture(new \ZekrBundle\DataFixtures\ORM\TaskManagerCommands());
        $this->addDataFixture(new \ZekrBundle\DataFixtures\ORM\LoadStaticPages());
    }
}