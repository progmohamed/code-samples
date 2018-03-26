<?php

namespace AdminBundle\Extension;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class AdminTwigExtension extends \Twig_Extension
{
    private $container;
    private $adminSections = null;
    private $securityChecker;

    public function __construct($container, AuthorizationCheckerInterface $securityChecker = null)
    {
        $this->container = $container;
        $this->securityChecker = $securityChecker;
    }   

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('base64_encode', [$this, 'base64Encode']),
            new \Twig_SimpleFilter('base64_decode', [$this, 'base64Decode']),
            new \Twig_SimpleFilter('fileSize', [$this, 'fileSize']),
            new \Twig_SimpleFilter('duration', [$this, 'duration']),
            new \Twig_SimpleFilter('json_decode', [$this, 'jsonDecode']),
            new \Twig_SimpleFilter('unserialize', [$this, 'unserialize']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('uniq_id', [$this, 'uniq_id']),
            new \Twig_SimpleFunction('getAdminSections', [$this, 'getAdminSections']),
            new \Twig_SimpleFunction('extractFirstImageFromHTML', [$this, 'extractFirstImageFromHTML']),
            new \Twig_SimpleFunction('is_granted_any', [$this, 'isGrantedAny']),
            new \Twig_SimpleFunction('getAdminHelper', [$this, 'getAdminHelper'], ['is_safe' => ['html']]),

        ];
    }

    public function getName()
    {
        return 'AdminTwigExtension';
    }

    public function getAdminHelper()
    {
        return $this->container->get('admin.helper');
    }


    public function base64Encode($str)
    {
        return base64_encode($str);
    }

    public function base64Decode($str)
    {
        return base64_decode($str);
    }

    public function uniq_id($prefix = "", $more_entropy = false)
    {
        return uniqid($prefix, $more_entropy);
    }

    public function getAdminSections()
    {
        if(is_null($this->adminSections)) {
            $em = $this->container->get('doctrine')->getManager();
            $this->adminSections = $em->getRepository('AdminBundle:Section')->findAll();
        }
        return $this->adminSections;
    }

    public function fileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    public function duration($sec)
    {
        $hours = floor($sec / (60 * 60));
        $divisor_for_minutes = $sec % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
        if ($hours   < 10) {$hours   = "0".$hours;}
        if ($minutes < 10) {$minutes = "0".$minutes;}
        if ($seconds < 10) {$seconds = "0".$seconds;}
        return $hours.':'.$minutes.':'.$seconds;
    }

    public function jsonDecode($str, $assoc = false )
    {
        return json_decode($str, $assoc);
    }

    public function unserialize($str)
    {
        return unserialize($str);
    }

    public function extractFirstImageFromHTML($html)
    {
        $crawler = new Crawler($html);
        $img = $crawler->filter('img')->first();
        if($img->count()) {
            return $img->attr('src');
        }
    }
    
    public function isGrantedAny(array $roles, $object = null, $field = null)
    {                       
        if (null === $this->securityChecker) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        try {
            foreach($roles as $role) {                         
                if($this->securityChecker->isGranted($role, $object)) {
                    return true;
                }
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

}