<?php

namespace AdminBundle\Service\AdminService;

use CommonBundle\Classes\PublicService;

class AdminService extends PublicService
{

    private $users;

    private $locale;

    public function getName()
    {
        return "المستخدمين";
    }

    public function getLocale()
    {
        $commonService = $this->container->get('common.service');
        if($commonService->bundleExists('LocaleBundle')) {
            if(!$this->locale) {
                $this->locale = new Locale($this->container);
            }
            return $this->locale;
        }
    }

    public function getUsers()
    {
        if (!isset($this->users)) {
            $em = $this->container->get('doctrine')->getManager();
            $users = $em->getRepository('AdminBundle:User')->getUsers();
            if ($users) {
                return $this->users = $users;
            } else {
                return null;
            }
        }
        return $this->users;
    }

    public function getUserById($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $user = $em->getRepository('AdminBundle:User')->getUserById($id);
        return $user;
    }

}