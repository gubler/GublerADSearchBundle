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
                ->scalarNode('ad_username')->defaultValue('')->end()
                ->scalarNode('ad_password')->defaultValue('')->end()
                ->scalarNode('ad_host')->defaultValue('')->end()
                ->scalarNode('ad_port')->defaultValue(3268)->end()
                ->scalarNode('ad_base_dn')->defaultValue('')->end()
                ->scalarNode('ad_search_class')->defaultValue('Gubler\ADSearchBundle\Domain\Search\ArraySearch')->end()
                ->scalarNode('ldap_adapter_class')->defaultValue('Gubler\ADSearchBundle\Domain\LdapAdapter\LdapArrayAdapter')->end()
                ->variableNode('test_users')
                    ->defaultValue(array())->end()
            ->end();

        return $treeBuilder;
    }
}
