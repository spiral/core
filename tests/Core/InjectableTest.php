<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Tests\Core;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Spiral\Core\ConfigsInterface;
use Spiral\Core\Container;
use Spiral\Core\Tests\Fixtures\InvalidInjector;
use Spiral\Core\Tests\Fixtures\SampleClass;
use Spiral\Core\Tests\Fixtures\TestConfig;

class InjectableTest extends TestCase
{
    /**
     * @expectedException \Spiral\Core\Exception\Container\AutowireException
     * @expectedExceptionMessage Undefined class or binding 'Spiral\Core\ConfigsInterface'
     */
    public function testMissingInjector()
    {
        $container = new Container();
        $container->get(TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\InjectionException
     * @expectedExceptionMessage Class 'Spiral\Core\Tests\Fixtures\InvalidInjector' must be an
     *                           instance of InjectorInterface for
     *                           'Spiral\Core\Tests\Fixtures\TestConfig'
     */
    public function testInvalidInjector()
    {
        $container = new Container();

        $container->bindInjector(TestConfig::class, InvalidInjector::class);
        $container->get(TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\AutowireException
     * @expectedExceptionMessage Undefined class or binding 'invalid-injector'
     */
    public function testInvalidInjectorBinding()
    {
        $container = new Container();

        $container->bindInjector(TestConfig::class, 'invalid-injector');
        $container->get(TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\InjectionException
     * @expectedExceptionMessage Class 'Spiral\Core\Tests\Fixtures\InvalidInjector' must be an
     *                           instance of InjectorInterface for
     *                           'Spiral\Core\Tests\Fixtures\TestConfig'
     */
    public function testInvalidRuntimeInjector()
    {
        $container = new Container();

        $container->bindInjector(TestConfig::class, 'invalid-injector');
        $container->bind('invalid-injector', new InvalidInjector());

        $container->get(TestConfig::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Injector can only be set as string binding
     */
    public function testInvalidInjectorArgument()
    {
        $container = new Container();

        $container->bindInjector(TestConfig::class, new InvalidInjector());

        $container->get(TestConfig::class);
    }

    public function testGetInjectors()
    {
        $container = new Container();

        $container->bindInjector(TestConfig::class, 'invalid-injector');

        $injectors = $container->getInjectors();

        $this->assertNotEmpty($injectors);
        $this->assertArrayHasKey(TestConfig::class, $injectors);
        $this->assertSame('invalid-injector', $injectors[TestConfig::class]);

        $container->removeInjector(TestConfig::class);
        $injectors = $container->getInjectors();

        $this->assertEmpty($injectors);
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\AutowireException
     * @expectedExceptionMessage Undefined class or binding 'invalid-configurator'
     */
    public function testInjectorOuterBinding()
    {
        $container = new Container();
        $container->bind(ConfigsInterface::class, 'invalid-configurator');

        $container->get(TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\InjectionException
     * @expectedExceptionMessage Invalid injection response for
     *                           'Spiral\Core\Tests\Fixtures\TestConfig'
     */
    public function testInvalidInjection()
    {
        $container = new Container();

        $configurator = m::mock(ConfigsInterface::class);
        $container->bind(ConfigsInterface::class, $configurator);

        $configurator->shouldReceive('createInjection')->andReturn(new SampleClass());

        $container->get(TestConfig::class);
    }

    public function testInjector()
    {
        $configurator = m::mock(ConfigsInterface::class);
        $expected = new TestConfig();

        $container = new Container();
        $container->bind(ConfigsInterface::class, $configurator);

        $configurator->shouldReceive('createInjection')->with(
            m::on(function (\ReflectionClass $r) {
                return $r->getName() == TestConfig::class;
            }),
            null
        )->andReturn($expected);

        $this->assertSame($expected, $container->get(TestConfig::class));
    }

    public function testInjectorWithContext()
    {
        $configurator = m::mock(ConfigsInterface::class);
        $expected = new TestConfig();

        $container = new Container();
        $container->bind(ConfigsInterface::class, $configurator);

        $configurator->shouldReceive('createInjection')->with(
            m::on(function (\ReflectionClass $r) {
                return $r->getName() == TestConfig::class;
            }),
            'context'
        )->andReturn($expected);

        $this->assertSame($expected, $container->get(TestConfig::class, 'context'));
    }

    public function testInjectorForMethod()
    {
        $configurator = m::mock(ConfigsInterface::class);
        $expected = new TestConfig();

        $container = new Container();
        $container->bind(ConfigsInterface::class, $configurator);

        $configurator->shouldReceive('createInjection')->with(
            m::on(function (\ReflectionClass $r) {
                return $r->getName() == TestConfig::class;
            }),
            'contextArgument'
        )->andReturn($expected);

        $arguments = $container->resolveArguments(new \ReflectionMethod($this, 'methodInjection'));

        $this->assertCount(1, $arguments);
        $this->assertSame($expected, $arguments[0]);
    }

    /**
     * @param TestConfig $contextArgument
     */
    private function methodInjection(TestConfig $contextArgument)
    {
    }
}
