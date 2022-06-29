<?php

declare(strict_types=1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle;

use Gubler\ADSearchBundle\Command\CreateTestUserJsonCommand;
use Gubler\ADSearchBundle\Service\ActiveDirectory\LdapFactory;
use Gubler\ADSearchBundle\Service\ActiveDirectory\ServerSearch;
use Gubler\ADSearchBundle\Service\ADSearchService;
use Gubler\ADSearchBundle\Service\ArraySearch\ArraySearch;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class GublerADSearchBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->enumNode('connection_type')
                    ->values(['array', 'server'])
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
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (class_exists(Application::class)) {
            $container->services()
                ->set('gubler_ad_search.command.create_test_user_json_command', CreateTestUserJsonCommand::class)
                ->tag('console.command');
        }

        if ('server' === $config['connection_type']) {
            $container->services()
                ->set('gubler_ad_search.ldap_factory', LdapFactory::class)
                ->arg(0, $config['config']['address'])
                ->arg(1, $config['config']['port'])
                ->arg(2, $config['config']['bind_user'])
                ->arg(3, $config['config']['bind_password'])
            ;
            $container->services()
                ->set('gubler_ad_search.search_adapter', ServerSearch::class)
                ->arg(0, service('gubler_ad_search.ldap_factory'))
            ;
        } elseif ('array' === $config['connection_type']) {
            $container->services()
                ->set('gubler_ad_search.search_adapter', ArraySearch::class)
                ->arg(0, $config['config']['test_users'])
            ;
        }

        $adapter = $container->services()->get('gubler_ad_search.search_adapter');
        $container->services()
            ->set('gubler_ad_search.ad_search', ADSearchService::class)
            ->arg(0, service('gubler_ad_search.search_adapter'))
            ->alias(ADSearchService::class, 'gubler_ad_search.ad_search')
        ;
    }
}
