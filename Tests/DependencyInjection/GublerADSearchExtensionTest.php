<?php

namespace Gubler\ADSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Gubler\ADSearchBundle\DependencyInjection\GublerADSearchExtension;

/**
 * Extension Test
 *
 * @package Gubler\ADSearchBundle\Tests\DependencyInjection
 */
class GublerADSearchExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function testAfterLoadingTheVersionParameterHasBeenSet()
    {
        $this->load(
            array(
                'ldap_username' => '',
                'ldap_password' => '',
                'ldap_host' => '',
                'ldap_port' => 3268,
                'ldap_base_dn' => '',
                'ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch',
            )
        );

        $this->assertContainerBuilderHasParameter('gubler_ad_search.ldap_username', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ldap_password', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ldap_host', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ldap_port', 3268);
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ldap_base_dn', '');
        $this->assertContainerBuilderHasParameter(
            'gubler_ad_search.ad_search_class',
            'Gubler\ADSearchBundle\Domain\Search\ArraySearch'
        );
    }

    /**
     * Get Container Extension
     *
     * @return array
     */
    protected function getContainerExtensions()
    {
        return array(
            new GublerADSearchExtension(),
        );
    }
}
