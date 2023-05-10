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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class GublerADSearchBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $definition->rootNode();
        $rootNode
            ->children()
                ->enumNode(name: 'connection_type')
                    ->values(values: ['array', 'server'])
                    ->info(info: 'Only accepts `array` or `server`')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode(name: 'config')
                    ->info(info: 'configuration')
                    ->children()
                        ->scalarNode(name: 'address')
                            ->info(info: 'Connection Type `server` only: Address of Active Directory Server')
                            ->defaultNull()
                        ->end()
                        ->integerNode(name: 'port')
                            ->info(info: 'Connection Type `server` only: Port to connect to Active Directory on')
                            ->defaultValue(value: 3268)
                        ->end()
                        ->scalarNode(name: 'bind_user')
                            ->info(info: 'Connection Type `server` only: Username to bind to AD with')
                            ->defaultNull()
                        ->end()
                        ->scalarNode(name: 'bind_password')
                            ->info(info: 'Connection Type `server` only: Password to bind to AD with')
                            ->defaultNull()
                        ->end()
                        ->arrayNode(name: 'secure')
                            ->treatNullLike(value: ['enable' => false, 'cert_path' => null])
                            ->children()
                                ->booleanNode(name: 'enable')
                                    ->info(info: 'Should the connection be made over TLS')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode(name: 'cert_path')
                                    ->info(info: 'Optional path to certificate file to be used for TLS connection')
                                    ->defaultNull()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode(name: 'test_users')
                            ->info(info: 'Connection Type `array` only: path to test users JSON file')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (class_exists(class: Application::class)) {
            $container->services()
                ->set(id: 'gubler_ad_search.command.create_test_user_json_command', class: CreateTestUserJsonCommand::class)
                ->tag(name: 'console.command');
        }

        if ('server' === $config['connection_type']) {
            $container->services()
                ->set(id: 'gubler_ad_search.ldap_factory', class: LdapFactory::class)
                ->arg(key: 0, value: $config['config']['address'])
                ->arg(key: 1, value: $config['config']['port'])
                ->arg(key: 2, value: $config['config']['bind_user'])
                ->arg(key: 3, value: $config['config']['bind_password'])
                ->arg(key: 4, value: $config['config']['secure']['enable'])
                ->arg(key: 5, value: $config['config']['secure']['cert_path'])
            ;
            $container->services()
                ->set(id: 'gubler_ad_search.search_adapter', class: ServerSearch::class)
                ->arg(key: 0, value: service(serviceId: 'gubler_ad_search.ldap_factory'))
            ;
        } elseif ('array' === $config['connection_type']) {
            if (null === $config['config']['test_users']) {
                throw new InvalidConfigurationException(message: 'You must define the `test_users` key when type is `array`');
            }

            $container->services()
                ->set(id: 'gubler_ad_search.search_adapter', class: ArraySearch::class)
                ->arg(key: 0, value: $config['config']['test_users'])
            ;
        }

        $adapter = $container->services()->get(id: 'gubler_ad_search.search_adapter');
        $container->services()
            ->set(id: 'gubler_ad_search.ad_search', class: ADSearchService::class)
            ->arg(key: 0, value: service(serviceId: 'gubler_ad_search.search_adapter'))
            ->alias(id: ADSearchService::class, referencedId: 'gubler_ad_search.ad_search')
        ;
    }
}
