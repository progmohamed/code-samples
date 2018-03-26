<?php

namespace ConfigBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigService implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    private $config = [];

    public function getValue($variable, $default = null)
    {
        if(!isset($this->config[$variable])) {
            $em = $this->container->get('doctrine')->getManager();
            $entity = $em->getRepository('ConfigBundle:ConfigVariable')->getVariableValue($variable, $default);
            $this->config[$variable] = $entity;
        }
        return $this->config[$variable];
    }
}