<?php

namespace LocaleBundle\DataFixtures\ORM;

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
            'Country List' => 'ROLE_LOCAL_COUNTRY_LIST',
            'Country New' => 'ROLE_LOCAL_COUNTRY_NEW',
            'Country Show' => 'ROLE_LOCAL_COUNTRY_SHOW',
            'Country Edit' => 'ROLE_LOCAL_COUNTRY_EDIT',
            'Country Delete' => 'ROLE_LOCAL_COUNTRY_DELETE',

            'Dialect List' => 'ROLE_LOCALE_DIALECT_LIST',
            'Dialect New' => 'ROLE_LOCALE_DIALECT_NEW',
            'Dialect Show' => 'ROLE_LOCALE_DIALECT_SHOW',
            'Dialect Edit' => 'ROLE_LOCALE_DIALECT_EDIT',
            'Dialect Delete' => 'ROLE_LOCALE_DIALECT_DELETE',

            'Language List' => 'ROLE_LOCALE_LANGUAGE_LIST',
            'Language New' => 'ROLE_LOCALE_LANGUAGE_NEW',
            'Language Show' => 'ROLE_LOCALE_LANGUAGE_SHOW',
            'Language Edit' => 'ROLE_LOCALE_LANGUAGE_EDIT',
            'Language Delete' => 'ROLE_LOCALE_LANGUAGE_DELETE',
        ];

        foreach ($groups as $k => $group) {
            $group = new Group($k, [$group]);
            $manager->persist($group);
        }
        $manager->flush();

    }
}