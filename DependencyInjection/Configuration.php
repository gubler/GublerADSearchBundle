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
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {

        $treeBuilder = new TreeBuilder('gubler_ad_search');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('gubler_ad_search');
        }

        $rootNode
            ->children()
                ->enumNode('connection_type')
                    ->values(array('array', 'server'))
                    ->info('Only accepts `array` or `server`')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('config')
                    ->info('configuration')
                    ->children()
                        ->scalarNode('address')
                            ->info('Connection Type `server` only: Address of Active Directory Server')
                        ->end()
                        ->scalarNode('port')
                            ->info('Connection Type `server` only: Port to connect to Active Directory on')
                        ->end()
                        ->scalarNode('bind_user')
                            ->info('Connection Type `server` only: Username to bind to AD with')
                        ->end()
                        ->scalarNode('bind_password')
                            ->info('Connection Type `server` only: Password to bind to AD with')
                        ->end()
                        ->scalarNode('test_users')
                            ->info('Connection Type `array` only: path to test users JSON file')
                        ->end()
                        ->arrayNode('secure')
                            ->treatNullLike(['enable' => false, 'cert_path' => null])
                            ->children()
                                ->booleanNode('enable')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('cert_path')
                                    ->defaultNull()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
