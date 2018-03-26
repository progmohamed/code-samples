<?php

namespace LocaleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LocaleBundle\Entity\Country;
use LocaleBundle\Entity\Language;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCountriesData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadCountries($manager);
    }

    private function loadCountries(ObjectManager $manager)
    {
        $languages = $this->container->get('locale.service')->getContentLanguages();
        $entity = new Country();
        foreach($languages as $language) {
            $entity->translate($language->getLocale())->setName('الإمارات العربية المتحدة');
        }
        $entity->mergeNewTranslations();
        $manager->persist($entity);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}