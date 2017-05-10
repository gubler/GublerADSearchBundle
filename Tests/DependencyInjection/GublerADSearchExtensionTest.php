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
                'ad_username' => '',
                'ad_password' => '',
                'ad_host' => '',
                'ad_port' => 3268,
                'ad_base_dn' => '',
                'ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch',
            )
        );

        $this->assertContainerBuilderHasParameter('gubler_ad_search.ad_username', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ad_password', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ad_host', '');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ad_port', 3268);
        $this->assertContainerBuilderHasParameter('gubler_ad_search.ad_base_dn', '');
        $this->assertContainerBuilderHasParameter(
            'gubler_ad_search.ad_search_class',
            'Gubler\ADSearchBundle\Domain\Search\ArraySearch'
        );
        $this->assertContainerBuilderHasParameter(
            'gubler_ad_search.ldap_adapter_class',
            'Gubler\ADSearchBundle\Domain\LdapAdapter\LdapArrayAdapter'
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
