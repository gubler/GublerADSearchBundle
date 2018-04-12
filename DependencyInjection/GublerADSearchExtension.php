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
use Symfony\Component\Console\Application;

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
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (class_exists(Application::class)) {
            $loader->load('console.xml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ('server' === $config['connection_type']) {
            $container->setParameter('gubler_ad_search.server.address', $config['config']['address']);
            $container->setParameter('gubler_ad_search.server.port', $config['config']['port']);
            $container->setParameter('gubler_ad_search.server.bind_user', $config['config']['bind_user']);
            $container->setParameter('gubler_ad_search.server.bind_password', $config['config']['bind_password']);
            $loader->load('server_services.xml');
        } elseif ('array' === $config['connection_type']) {
            $container->setParameter('gubler_ad_search.array.test_users', $config['config']['test_users']);
            $loader->load('array_services.xml');
        }
    }
}
