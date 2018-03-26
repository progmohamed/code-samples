<?php

namespace TaskManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Entity\Group;

class LoadGroups implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $groups = [
            'Task Viewer List' => 'ROLE_TASKMANAGER_TASKVIEWER_LIST',
        ];

        foreach ($groups as $k => $group) {
            $group = new Group($k, [$group]);
            $manager->persist($group);
        }
        $manager->flush();

    }
}