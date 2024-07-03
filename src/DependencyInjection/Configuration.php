<?php
namespace Snoke\OAuth\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('snoke_oauth');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->end()
            ->end();


        return $treeBuilder;
    }
}
