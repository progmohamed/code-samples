<?php

namespace AdminBundle\Service\AdminService;

use LocaleBundle\Classes\AbstractLocaleHelper;

class Locale extends AbstractLocaleHelper
{
    public function getCountryRestrictions($country)
    {
        $em = $this->container->get('doctrine')->getManager();
        $comment = $em->getRepository('AdminBundle:User');
        return $comment->getUserCountryCount($country);
    }
}