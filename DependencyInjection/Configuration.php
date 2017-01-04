<?php
/**
 * Bundle Config Loader
 */
namespace Gubler\ADSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle Configuration
 * @package Gubler\ADSearchBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gubler_ad_search');

        $rootNode
            ->children()
                ->scalarNode('ldap_username')->defaultValue('')->end()
                ->scalarNode('ldap_password')->defaultValue('')->end()
                ->scalarNode('ldap_host')->defaultValue('')->end()
                ->scalarNode('ldap_port')->defaultValue(3268)->end()
                ->scalarNode('ldap_base_dn')->defaultValue('')->end()
                ->scalarNode('ad_search_class')->defaultValue('Gubler\ADSearchBundle\Domain\Search\ArraySearch')->end()
                ->variableNode('test_users')
                    ->defaultValue(array())->end()
            ->end();

        return $treeBuilder;
    }
}
