<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\DependencyInjection;

use Gubler\ADSearchBundle\DependencyInjection\GublerADSearchExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * GublerADSearchExtensionTest
 */
class GublerADSearchExtensionTest extends AbstractExtensionTestCase
{
    /**
     * Load Array Config
     */
    public function loadArrayConfig()
    {
        parent::load([
            'connection_type' => 'array',
            'config' => [
                'test_users' => 'testUsers.json',
            ],
        ]);
    }

    /**
     * Load Server Config
     */
    public function loadServerConfig()
    {
        parent::load([
            'connection_type' => 'server',
            'config' => [
                'address' => 'test_server',
                'port' => 3268,
                'bind_user' => 'testUser',
                'bind_password' => 'password',
            ],
        ]);
    }

    /**
     * @test
     */
    public function correctParametersLoadedForServerConfig()
    {
        $this->loadServerConfig();

        $this->assertContainerBuilderHasParameter('gubler_ad_search.server.port', 3268);
        $this->assertContainerBuilderHasParameter('gubler_ad_search.server.bind_user', 'testUser');
        $this->assertContainerBuilderHasParameter('gubler_ad_search.server.bind_password', 'password');
    }

    /**
     * @test
     */
    public function correctParametersLoadedForArrayConfig()
    {
        $this->loadArrayConfig();

        $this->assertContainerBuilderHasParameter('gubler_ad_search.array.test_users', 'testUsers.json');
    }

    /**
     * Load the container extension
     *
     * @return array
     */
    protected function getContainerExtensions(): array
    {
        return array(
            new GublerADSearchExtension(),
        );
    }
}
