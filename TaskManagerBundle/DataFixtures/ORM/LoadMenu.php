<?php

namespace TaskManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Entity\Section;
use AdminBundle\Entity\SectionItem;

class LoadMenu implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $section = new Section();
        $section->setIdentifier('task-manager')
                ->setTitle('task_mager.menu.task_mager')
                ->setImage('bundles/taskmanager/images/dashboard/small/task_queue.png')
                ->setDescription('task_mager.hints.task_mager')
                ->setDescriptionSort(5);

        $item = new SectionItem();
        $item   ->setTitle('task_mager.menu.task_viewer')
                ->setRoute('taskmanager_taskviewer_list')
                ->setImage('bundles/taskmanager/images/dashboard/task_queue.png')
                ->addNewRoleByRoleName('ROLE_TASKMANAGER_TASKVIEWER_LIST');
        $section->addItem($item);

        $manager->persist($section);
        $manager->flush();

    }
}