<?php

namespace SearchBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SolrConfigService
{
    private $port;
    private $legacyPostJar;
    private $linuxPostCommand;
    private $env;
    private $url;


    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->legacyPostJar = $this->container->getParameter('search.solr_legacy_post_jar');
        $this->linuxPostCommand = $this->container->getParameter('search.solr_linux_post_command');
        $this->env = $this->container->getParameter('search.solr_env');
        $this->url = $this->container->getParameter('search.solr_url');
        if(substr($this->url, -1) != '/') {
            $this->url .= '/';
        }
    }

    public function getPort()
    {
        if(is_null($this->port)) {
            $port = parse_url($this->url, PHP_URL_PORT);
            if (empty($port)) {
                $port = '8983';
            }
            $this->port = $port;
        }
        return $this->port;
    }

    public function getLegacyPostJar()
    {
        return $this->legacyPostJar;
    }

    public function getLinuxPostCommand()
    {
        return $this->linuxPostCommand;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function getUrl()
    {
        return $this->url;
    }

    //TODO delete this in other tome for SOA Reasons
//    public function getCoreNameByLocale($locale)
//    {
//        $locale = strtolower($locale);
//        return $this->container->getParameter('search.solr_cores.'.$locale);
//    }

    public function getFullPostCommand($clientName, $xmlFilePath)
    {
        if('windows' == $this->getEnv()) {
            $command = "java -Durl=".$this->getUrl().$clientName."/update -Dtype=application/xml -jar ".$this->getLegacyPostJar()." {$xmlFilePath} ";
        }else{
            $command = $this->getLinuxPostCommand(). " -c {$clientName} -p ".$this->getPort()." {$xmlFilePath}";
        }
        return $command;
    }

}