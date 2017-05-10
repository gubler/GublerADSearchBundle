<?php
namespace Gubler\ADSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Gubler\ADSearchBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Test ADSearchBundle Configuration
 *
 * @package Gubler\ADSearchBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Test
     */
    public function testConfigurationReturnsTreeBuilder()
    {
        $config = new Configuration();

        /** @var TreeBuilder $result */
        $result = $config->getConfigTreeBuilder();

        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $result);
    }

    /**
     * Test
     */
    public function testConfigurationValidIfNoValuesSupplied()
    {
        $this->assertConfigurationIsValid(
            array(
                array(), // no values at all
            )
        );
    }

    /**
     * Test
     */
    public function testProcessedValueContainsRequiredValue()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('ad_username' => ''),
                array('ad_password' => ''),
                array('ad_host' => ''),
                array('ad_port' => 3268),
                array('ad_base_dn' => ''),
                array('ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch'),
                array('ldap_adapter_class' => 'Gubler\ADSearchBundle\Domain\LdapAdapter\LdapAdapter'),
                array('test_users' => array()),
            ),
            array(
                'ad_username' => '',
                'ad_password' => '',
                'ad_host' => '',
                'ad_port' => 3268,
                'ad_base_dn' => '',
                'ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch',
                'ldap_adapter_class' => 'Gubler\ADSearchBundle\Domain\LdapAdapter\LdapAdapter',
                'test_users' => array(),
            )
        );
    }

    /**
     * @return Configuration
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
