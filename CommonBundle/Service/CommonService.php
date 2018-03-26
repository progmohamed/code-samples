<?php

namespace CommonBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CommonService
{
    use ContainerAwareTrait;

    public function bundleExists($bundle)
    {
        return array_key_exists($bundle, $this->container->getParameter('kernel.bundles'));
    }

    public function log($serviceName, $message, $parameters = null, $user = null)
    {
        if($this->bundleExists('LogBundle')) {
            $this->container
                ->get('log.service')
                ->log($serviceName, $message, $parameters, $user);
        }
    }

}