<?php
/**
 * DI Extension
 */
namespace Gubler\ADSearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * @package Gubler\ADSearchBundle\DependencyInjection
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

        $container->setParameter('gubler_ad_search.ldap_username', $config['ldap_username']);
        $container->setParameter('gubler_ad_search.ldap_password', $config['ldap_password']);
        $container->setParameter('gubler_ad_search.ldap_host', $config['ldap_host']);
        $container->setParameter('gubler_ad_search.ldap_port', $config['ldap_port']);
        $container->setParameter('gubler_ad_search.ldap_base_dn', $config['ldap_base_dn']);
        $container->setParameter('gubler_ad_search.ad_search_class', $config['ad_search_class']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
