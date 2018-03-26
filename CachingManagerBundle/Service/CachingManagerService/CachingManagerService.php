<?php

namespace CachingManagerBundle\Service\CachingManagerService;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Predis\Client;

class CachingManagerService
{
    private $client;
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $scheme = $this->container->getParameter('caching_manager.redis_scheme');
        $host = $this->container->getParameter('caching_manager.redis_host');
        $port = $this->container->getParameter('caching_manager.redis_port');
        $db = $this->container->getParameter('caching_manager.redis_database');
        $this->client = new Client([
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'database' => $db,
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }

}