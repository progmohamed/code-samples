<?php

namespace LocaleBundle\Service\LocaleService;

use CommonBundle\Classes\PublicService;

class LocaleService extends PublicService
{

    private $countries = [];
    private $country = [];
    private $languages = null;
    private $languagesLocales = [];
    private $languageById = [];
    private $dialectsById = [];
    private $dialectsByLanguageId = [];

    public function getName()
    {
        return "الموقع";
    }

    public function getCountries($locale)
    {
        if(!isset($this->countries[$locale])) {
            $em = $this->container->get('doctrine')->getManager();
            $countryRepository = $em->getRepository('LocaleBundle:Country');
            $countries = $countryRepository->getCountryArray($locale);
            $out = [];
            foreach($countries as $country) {
                $out[$country['id']] = $country['translations'][$locale]['name'];
            }
            $this->countries[$locale] = $out;
        }
        return $this->countries[$locale];
    }

    public function getCountryArrayById($id, $locale)
    {
        if(!isset($this->country[$id])) {
            $em = $this->container->get('doctrine')->getManager();
            $countryRepository = $em->getRepository('LocaleBundle:Country');
            $country = $countryRepository->getCountryArrayById($id, $locale);

            $this->country[$id] = $country['translations'][$locale]['name'];
        }
        return $this->country[$id];
    }


    public function getLanguages()
    {
        if(is_null($this->languages)) {
            $em = $this->container->get('doctrine')->getManager();
            $languageRepository = $em->getRepository('LocaleBundle:Language');
            $this->languages = $languageRepository->findBy([], ['name' => 'ASC']);
        }
        return $this->languages;
    }

    public function getContentLanguages()
    {
        $out = [];
        $languages = $this->getLanguages();
        /** @var  Language $language */
        foreach($languages as $language) {
            if($language->getTranslateContent()) {
                $out[] = $language;
            }
        }
        return $out;
    }

    public function getFrontEndSwitchLanguages()
    {
        $out = [];
        $languages = $this->getLanguages();
        /** @var  Language $language */
        foreach($languages as $language) {
            if($language->getSwitchFrontEnd()) {
                $out[] = $language;
            }
        }
        return $out;
    }

    public function getBackEndSwitchLanguages()
    {
        $out = [];
        $languages = $this->getLanguages();
        /** @var  Language $language */
        foreach($languages as $language) {
            if($language->getSwitchBackEnd()) {
                $out[] = $language;
            }
        }
        return $out;
    }

    public function getLanguageByLocale($locale)
    {
        $locale = strtolower($locale);
        if(!isset($this->languagesLocales[$locale])) {
            $em = $this->container->get('doctrine')->getManager();
            $languageRepository = $em->getRepository('LocaleBundle:Language');
            $language = $languageRepository->findOneByLocale($locale);
            if($language) {
                return $this->languagesLocales[$locale] = $language;
            }else{
                return null;
            }
        }
        return $this->languagesLocales[$locale];
    }

    public function getLanguageById($id)
    {
        if(!isset($this->languageById[$id])) {
            $em = $this->container->get('doctrine')->getManager();
            $languageRepository = $em->getRepository('LocaleBundle:Language');
            $language = $languageRepository->find($id);
            if($language) {
                return $this->languageById[$id] = $language;
            }else{
                return null;
            }
        }
        return $this->languageById[$id];
    }

    public function getLanguageIdByLocale($locale)
    {
        $em = $this->container->get('doctrine')->getManager();
        $languageRepository = $em->getRepository('LocaleBundle:Language');
        return $languageRepository->getLanguageIdByLocale($locale);
    }


    public function getDialectByLanguageId($languageId, $locale)
    {
        if(!isset($this->dialectsByLanguageId[$languageId])) {
            $em = $this->container->get('doctrine')->getManager();
            $dialectRepository = $em->getRepository('LocaleBundle:Dialect');
            $dialects = $dialectRepository->getDialectArray($languageId, $locale);

            $out = [];
            foreach($dialects as $dialect) {
                $out[$dialect['id']] = $dialect['translations'][$locale]['name'];
            }

            if ($out) {
                return $this->dialectsByLanguageId[$languageId] = $out;
            } else {
                return null;
            }
        }
        return $this->dialectsByLanguageId[$languageId];
    }

    public function getDialectById($id)
    {
        if(!isset($this->dialectsById[$id])) {
            $em = $this->container->get('doctrine')->getManager();
            $dialectRepository = $em->getRepository('LocaleBundle:Dialect');
            $dialect= $dialectRepository->find($id);
            if($dialect) {
                return $this->dialectsById[$id] = $dialect;
            }else{
                return null;
            }
        }
        return $this->dialectsById[$id];
    }

}