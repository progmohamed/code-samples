<?php

namespace CachingManagerBundle\DataFixtures\ORM;

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
            'Caching List' => 'ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_LIST',
            'Caching Delete' => 'ROLE_CACHINGMANAGEMENT_CACHINGMANAGEMENT_DELETE',
        ];

        foreach ($groups as $k => $group) {
            $group = new Group($k, [$group]);
            $manager->persist($group);
        }
        $manager->flush();

    }
}