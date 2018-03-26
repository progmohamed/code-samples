<?php

namespace ZekrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Entity\Section;
use AdminBundle\Entity\SectionItem;

class TaskManagerCommands implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $taskmanager = $this->container->get('taskmanager.service');
        $taskmanager->addTaskRunImmediately('zekr:remove-temp-video', [], 'Removing temp-video', 'h 24');

    }
}