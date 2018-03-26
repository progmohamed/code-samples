<?php

namespace AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('admin');
        $user->setRealName('admin');
        $user->setDateOfBirth(new \DateTime());
        $user->setSex(1);
        $user->setPhoneNumber('123456');
        $user->setEmail('admin@domain.com');
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $userManager->updateUser($user, true);

        for($i = 1 ; $i <= 30; $i++) {
            $user = $userManager->createUser();
            $user->setUsername('admin'.$i);
            $user->setRealName('admin'.$i);
            $user->setDateOfBirth(new \DateTime());
            $user->setSex(1);
            $user->setPhoneNumber('123456');
            $user->setEmail('admin'.$i.'@domain.com');
            $user->setPlainPassword('admin');
            $user->setEnabled(true);
            $userManager->updateUser($user, true);
        }
    }
}