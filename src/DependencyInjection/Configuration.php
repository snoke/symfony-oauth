<?php
namespace Snoke\OAuth\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('snoke_o_auth');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->arrayNode('google')->children()
            ->scalarNode('success')->end()
            ->scalarNode('apiKey')->end()
            ->arrayNode('style')->children()
            ->scalarNode('theme')->end()
            ->scalarNode('width')->end()
            ->scalarNode('locale')->end()
            ->scalarNode('type')->end()
            ->scalarNode('size')->end()
            ->scalarNode('text')->end()
            ->scalarNode('shape')->end()
            ->scalarNode('logo_alignment')->end()
            ->end()
            ->end()
            ->end()
            ->end();


        return $treeBuilder;
    }
}
