<?php

namespace AdminBundle\DataFixtures\ORM;

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
            'Super Admin' => 'ROLE_SUPER_ADMIN',
            'Admin' => 'ROLE_ADMIN',
            'Users List' => 'ROLE_ADMIN_USER_INDEX',
            'Users Add' => 'ROLE_ADMIN_USER_ADD',
            'Users Edit' => 'ROLE_ADMIN_USER_EDIT',
            'Users Show' => 'ROLE_ADMIN_USER_SHOW',
            'Users Delete' => 'ROLE_ADMIN_USER_DELETE',
        ];

        foreach ($groups as $k => $group) {
            $group = new Group($k, [$group]);
            $manager->persist($group);
        }
        $manager->flush();

    }
}