<?php

namespace LocaleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LocaleBundle\Entity\Country;
use LocaleBundle\Entity\Language;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLanguagesData implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadLanguages($manager);
    }

    private function loadLanguages(ObjectManager $manager)
    {
        $languages = [
            ['ar', 'العربية', 'rtl'],
            ['fa', 'فارسي', 'rtl'],
            ['en', 'English', 'ltr'],
        ];

        foreach($languages as $element) {
            $language = new Language();
            $language->setLocale($element[0]);
            $language->setName($element[1]);
            $language->setDirection($element[2]);
            $language->setSwitchBackEnd(true);
            $language->setSwitchFrontEnd(true);
            $language->setTranslateContent(true);
            $manager->persist($language);
            $manager->flush();
        }
    }

}