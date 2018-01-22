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
                ->enumNode('connection_type')
                    ->values(array('array', 'server'))
                    ->info('Only accepts `array` or `server`')
                    ->defaultValue('array')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('array_test_users')
                    ->info('Connection Type `array` only: path to test users JSON file')
                    ->defaultNull()
                ->end()
                ->scalarNode('server_address')
                    ->info('Connection Type `server` only: Address of Active Directory Server')
                    ->defaultNull()
                ->end()
                ->integerNode('server_port')
                    ->info('Connection Type `server` only: Port to connect to Active Directory on')
                    ->defaultValue(3268)
                ->end()
                ->scalarNode('server_bind_user_dn')
                    ->info('Connection Type `server` only: Username to bind to AD with')
                    ->defaultNull()
                ->end()
                ->scalarNode('server_bind_user_password')
                    ->info('Connection Type `server` only: Password to bind to AD with')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
