<?php

namespace ConfigBundle\DataFixtures\ORM;

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
        $section->setIdentifier('config')
                ->setTitle('config.menu.config')
                ->setImage('bundles/config/images/dashboard/small/config.png')
                ->setDescription('config.hints.config')
                ->setDescriptionSort(5);

        $item = new SectionItem();
        $item   ->setTitle('config.menu.config')
                ->setRoute('zekr_config_variable_list')
                ->setImage('bundles/config/images/dashboard/config.png')
                ->addNewRoleByRoleName('ROLE_SUPER_ADMIN');
        $section->addItem($item);

        $manager->persist($section);
        $manager->flush();

    }
}