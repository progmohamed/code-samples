<?php

namespace CachingManagerBundle\DataFixtures\ORM;

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
        $section->setIdentifier('caching_management')
                ->setTitle('caching.menu.management')
                ->setImage('bundles/cachingmanager/images/dashboard/small/caching.png')
                ->setDescription('caching.hints.management')
                ->setDescriptionSort(3);

        $item = new SectionItem();
        $item   ->setTitle('caching.menu.parts_management')
            ->setRoute('cachingmanager_cachingmanagement_list')
            ->setImage('bundles/cachingmanager/images/dashboard/parts_management.png')
            ->addNewRoleByRoleName('ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_LIST')
            ->setDescription('caching.hints.parts_management')
            ->setDescriptionSort(4);
        $section->addItem($item);


        $manager->persist($section);
        $manager->flush();

    }
}