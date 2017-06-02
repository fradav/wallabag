<?php

namespace Wallabag\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wallabag_core');

        $rootNode
            ->children()
                ->arrayNode('languages')
                    ->prototype('scalar')->end()
                ->end()
                ->integerNode('items_on_page')
                    ->defaultValue(12)
                ->end()
                ->scalarNode('theme')
                    ->defaultValue('material')
                ->end()
                ->scalarNode('language')
                    ->defaultValue('en')
                ->end()
                ->integerNode('rss_limit')
                    ->defaultValue(50)
                ->end()
                ->integerNode('reading_speed')
                    ->defaultValue(1)
                ->end()
                ->scalarNode('version')
                ->end()
                ->scalarNode('paypal_url')
                ->end()
                ->integerNode('cache_lifetime')
                    ->defaultValue(10)
                ->end()
                ->scalarNode('fetching_error_message')
                ->end()
                ->scalarNode('fetching_error_message_title')
                ->end()
                ->scalarNode('action_mark_as_read')
                    ->defaultValue(1)
                ->end()
                ->scalarNode('list_mode')
                    ->defaultValue(1)
                ->end()
                ->scalarNode('notifications_nb')
                    ->defaultValue(5)
                ->end()
                ->scalarNode('api_limit_mass_actions')
                    ->defaultValue(10)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
