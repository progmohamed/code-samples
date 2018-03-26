<?php

namespace LocaleBundle\Classes;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractLocaleHelper implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    function __construct($container)
    {
        $this->setContainer($container);
    }


    public function getCountryRestrictions($country)
    {
        return 0;
    }

    public function getDialectRestrictions($dialect)
    {
        return 0;
    }

    public function getLanguageRestrictions($language)
    {
        return 0;
    }

}