<?php

declare(strict_types=1);

namespace Spiral\Tests\Core;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Spiral\Core\ConfigsInterface;
use Spiral\Core\Container;
use Spiral\Tests\Core\Fixtures\Factory;
use Spiral\Tests\Core\Fixtures\SampleClass;

class BindingsTest extends TestCase
{
    public function testBasicBinding(): void
    {
        $container = new Container();
        $this->assertInstanceOf(ContainerInterface::class, $container);

        $this->assertFalse($container->has('abc'));

        $container->bind('abc', fn() => 'hello');

        $this->assertTrue($container->has('abc'));
        $this->assertEquals('hello', $container->get('abc'));
    }

    public function testStringBinding(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('abc'));
        $container->bind('abc', fn() => 'hello');

        $container->bind('dce', 'abc');

        $this->assertTrue($container->has('dce'));
        $this->assertEquals('hello', $container->get('abc'));
        $this->assertEquals($container->get('abc'), $container->get('dce'));
    }

    public function testClassBinding(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('sampleClass'));
        $container->bind('sampleClass', SampleClass::class);

        $this->assertTrue($container->has('sampleClass'));
        $this->assertInstanceOf(SampleClass::class, $container->get('sampleClass'));
    }

    public function testFactoryBinding(): void
    {
        $container = new Container();

        $container->bindSingleton('sampleClass', [Factory::class, 'sampleClass']);
        $this->assertInstanceOf(SampleClass::class, $container->get('sampleClass'));
    }

    public function testInstanceBinding(): void
    {
        $container = new Container();

        $container->bindSingleton('sampleClass', new SampleClass());

        $instance = $container->get('sampleClass');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('sampleClass'));
    }

    public function testAutoScalarBinding(): void
    {
        $container = new Container();
        $container->bind(ConfigsInterface::class, 42.69);

        self::assertSame(42.69, $container->get(ConfigsInterface::class));
    }
}
