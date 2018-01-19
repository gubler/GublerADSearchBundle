<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle Configuration
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
            ->scalarNode('ad_search_class')->defaultValue(
                'Gubler\ADSearchBundle\Domain\Search\ArraySearchInterface'
            )->end()
            ->scalarNode('ldap_adapter_class')->defaultValue('Gubler\ADSearchBundle\Domain\LdapAdapter\LdapArrayAdapter')->end()
            ->variableNode('test_users')->defaultValue(array())->end()
            ->end();

        return $treeBuilder;
    }
}
