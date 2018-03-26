<?php

namespace ConfigBundle\DataFixtures\ORM;

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
        $em = $this->container->get('doctrine')->getManager();
        $em->createQuery('DELETE ConfigBundle:ConfigVariable c')->execute();
        $variable1 = new ConfigVariable();
        $variable1->setVariable('rowsInPage')
            ->setType(ConfigVariable::NUMERIC)
            ->setValue(10);
        $manager->persist($variable1);

        $variable2 = new ConfigVariable();
        $variable2->setVariable('convertVideo')
            ->setType(ConfigVariable::BOOLEAN)
            ->setValue(1);
        $manager->persist($variable2);

        $variable2 = new ConfigVariable();
        $variable2->setVariable('inlineEditingMode')
            ->setType(ConfigVariable::CHOICE)
            ->setData(json_encode(['inline'=>'inline', 'popup'=>'popup']))
            ->setValue('inline');
        $manager->persist($variable2);

        $languages = $this->container->get('admin.admin_helper')->getLanguages();
        $languageArr = [];
        foreach($languages as $language) {
            $languageArr[$language->getLocale()] = $language->getLocale();
        }
        /**
         * @var $languageArr array
         */
        $variable2 = new ConfigVariable();
        $variable2->setVariable('defaultLocale')
            ->setType(ConfigVariable::CHOICE)
            ->setData(json_encode($languageArr))
            ->setValue(reset($languageArr));
        $manager->persist($variable2);

        $variable2 = new ConfigVariable();
        $variable2->setVariable('sessionMaxIdleTime')
            ->setType(ConfigVariable::NUMERIC)
            ->setValue(900);
        $manager->persist($variable2);

        $manager->flush();
    }
}