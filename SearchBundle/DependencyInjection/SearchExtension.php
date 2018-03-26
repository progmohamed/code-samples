<?php

namespace SearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SearchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if('windows' == $config['solr_env']) {
            if(empty($config['solr_legacy_post_jar'])) {
                throw new \Exception('You must set value to search.solr_legacy_post_jar');
            }
        }
        if('linux' == $config['solr_env']) {
            if(empty($config['solr_linux_post_command'])) {
                throw new \Exception('You must set value to search.solr_linux_post_command');
            }
        }

        $container->setParameter('search.solr_legacy_post_jar', $config['solr_legacy_post_jar']);
        $container->setParameter('search.solr_linux_post_command', $config['solr_linux_post_command']);
        $container->setParameter('search.solr_env', $config['solr_env']);
        $container->setParameter('search.solr_url', $config['solr_url']);
    }
}
