<?php

namespace AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ConfigBundle\Entity\ConfigVariable;

class LoadConfigData implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $sessionMaxIdleTimeVariable = new ConfigVariable();
        $sessionMaxIdleTimeVariable->setVariable('sessionMaxIdleTime')
            ->setType(ConfigVariable::TYPE_NUMERIC)
            ->setValue(999999)
            ->setSectionTranslation('admin.config.general_section')
            ->setVariableTranslation('admin.config.session_max_idle_time')
            ->setScope(ConfigVariable::SCOPE_GLOBAL);
        $manager->persist($sessionMaxIdleTimeVariable);

        $adminEmailVariable = new ConfigVariable();
        $adminEmailVariable->setVariable('adminEmail')
            ->setType(ConfigVariable::TYPE_STRING)
            ->setValue(null)
            ->setSectionTranslation('admin.config.general_section')
            ->setVariableTranslation('admin.config.admin_email')
            ->setScope(ConfigVariable::SCOPE_GLOBAL);
        $manager->persist($adminEmailVariable);

        $manager->flush();
    }
}