<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Tests\Unit\DependencyInjection;

use Gubler\ADSearchBundle\DependencyInjection\GublerADSearchExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * GublerADSearchExtensionTest
 */
class GublerADSearchExtensionTest extends TestCase
{
    /**
     * @var GublerADSearchExtension
     */
    private $extension;
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new GublerADSearchExtension();
        $this->container = new ContainerBuilder();
        $this->container->set('annotations.cached_reader', new \stdClass());
        $this->container->registerExtension($this->extension);
    }
    /**
     * Test load extension
     */
    public function testLoadExtension(): void
    {
        $this->container->prependExtensionConfig($this->extension->getAlias(), ['auto_filter_forms' => true]);
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
        // Check that services have been loaded
        static::assertTrue($this->container->has('bukashk0zzz_filter.filter'));
    }
}
