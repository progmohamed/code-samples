<?php

namespace SearchBundle\DataFixtures\ORM;

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
        $section->setIdentifier('search')
                ->setTitle('search.menu.search')
                ->setImage('bundles/search/images/dashboard/small/search.png')
                ->setDescription('search.hints.search')
                ->setDescriptionSort(3);

        $item = new SectionItem();
        $item   ->setTitle('search.menu.history')
            ->setRoute('search_history_list')
            ->setImage('bundles/search/images/dashboard/history.png')
            ->addNewRoleByRoleName('ROLE_SEARCH_HISTORY_LIST')
            ->setDescription('search.hints.history')
            ->setDescriptionSort(4);
        $section->addItem($item);



        $manager->persist($section);
        $manager->flush();

    }
}