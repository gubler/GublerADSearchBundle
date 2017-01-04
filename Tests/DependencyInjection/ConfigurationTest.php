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
                array('ldap_username' => ''),
                array('ldap_password' => ''),
                array('ldap_host' => ''),
                array('ldap_port' => 3268),
                array('ldap_base_dn' => ''),
                array('ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch'),
                array('test_users' => array()),
            ),
            array(
                'ldap_username' => '',
                'ldap_password' => '',
                'ldap_host' => '',
                'ldap_port' => 3268,
                'ldap_base_dn' => '',
                'ad_search_class' => 'Gubler\ADSearchBundle\Domain\Search\ArraySearch',
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
