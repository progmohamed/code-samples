<?php

namespace ZekrBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zekr');

        $rootNode
            ->children()
                ->scalarNode('ffmpeg_binary')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('solr_legacy_post_jar')->end()
                ->scalarNode('solr_linux_post_command')->end()
                ->enumNode('solr_env')
                    ->values(['windows', 'linux'])
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('solr_url')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
