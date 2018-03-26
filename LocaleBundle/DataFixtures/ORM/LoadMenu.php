<?php

namespace LocaleBundle\DataFixtures\ORM;

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
        $section->setIdentifier('locale_management')
                ->setTitle('locale.menu.locale')
                ->setImage('bundles/locale/images/dashboard/small/locale.png')
                ->setDescription('locale.hints.locale')
                ->setDescriptionSort(3);

        $item = new SectionItem();
        $item   ->setTitle('locale.menu.countries')
            ->setRoute('locale_country')
            ->setImage('bundles/locale/images/dashboard/country.png')
            ->addNewRoleByRoleName('ROLE_LOCAL_COUNTRY_LIST')
            ->setDescription('locale.hints.countries')
            ->setDescriptionSort(4);
        $section->addItem($item);


        $item = new SectionItem();
        $item   ->setTitle('locale.menu.langs')
            ->setRoute('locale_language_list')
            ->setImage('bundles/locale/images/dashboard/language.png')
            ->addNewRoleByRoleName('ROLE_LOCALE_LANGUAGE_LIST')
            ->setDescription('locale.hints.langs')
            ->setDescriptionSort(5);
        $section->addItem($item);


        $item = new SectionItem();
        $item   ->setTitle('locale.menu.dialect')
            ->setRoute('locale_dialect_list')
            ->setImage('bundles/locale/images/dashboard/dialect.png')
            ->addNewRoleByRoleName('ROLE_LOCALE_DIALECT_LIST')
            ->setDescription('locale.hints.dialect')
            ->setDescriptionSort(6);
        $section->addItem($item);

        $manager->persist($section);
        $manager->flush();

    }
}