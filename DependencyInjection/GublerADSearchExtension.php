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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration
 */
class GublerADSearchExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('gubler_ad_search.connection_type', $config['connection_type']);
        $container->setParameter('gubler_ad_search.array_test_users', $config['array_test_users']);
        $container->setParameter('gubler_ad_search.server_address', $config['server_address']);
        $container->setParameter('gubler_ad_search.server_port', $config['server_port']);
        $container->setParameter('gubler_ad_search.server_bind_user', $config['server_bind_user']);
        $container->setParameter('gubler_ad_search.server_bind_password', $config['server_bind_password']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        if ($config['connection_type'] === 'array') {
            $loader->load('array_services.xml');
        } else {
            $loader->load('server_services.xml');
        }
    }
}
