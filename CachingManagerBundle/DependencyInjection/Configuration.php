<?php

namespace CachingManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('caching_manager');
        $rootNode
            ->children()
                ->scalarNode('redis_scheme')->defaultValue('tcp')->end()
                ->scalarNode('redis_host')->defaultValue('127.0.0.1')->end()
                ->integerNode('redis_port')->defaultValue(6379)->end()
                ->integerNode('redis_database')->defaultValue(15)->end()
            ->end();
        return $treeBuilder;
    }
}
