<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Container;
use Spiral\Core\Exception\Container\ArgumentException;
use Spiral\Core\Exception\Container\AutowireException;
use Spiral\Core\Exception\Container\ContainerException;
use Spiral\Core\Exception\DependencyException;
use Spiral\Core\Exception\LogicException;

class ExceptionsTest extends TestCase
{
    /**
     * @expectedException \Spiral\Core\Exception\Container\ContainerException
     * @expectedExceptionMessage Invalid binding for 'invalid'
     */
    public function testInvalidBinding()
    {
        $container = new Container();
        $container->bind('invalid', ['invalid']);
        $container->get('invalid');
    }

    /**
     * @expectedException LogicException
     */
    public function testClone()
    {
        $container = new Container();
        clone $container;
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\ContainerException
     * @expectedExceptionMessage Class Spiral\Core\Tests\InvalidClass does not exist
     */
    public function testInvalidInjectionParameter()
    {
        $container = new Container();

        $container->resolveArguments(new \ReflectionMethod($this, 'invalidInjection'));
    }

    public function testArgumentException(string $param = null)
    {
        $method = new \ReflectionMethod($this, 'testArgumentException');

        $e = new ArgumentException(
            $method->getParameters()[0],
            $method
        );

        $this->assertInstanceOf(AutowireException::class, $e);
        $this->assertInstanceOf(ContainerException::class, $e);
        $this->assertInstanceOf(DependencyException::class, $e);
        $this->assertInstanceOf(ContainerExceptionInterface::class, $e);

        $this->assertSame($method, $e->getContext());
        $this->assertSame('param', $e->getParameter()->getName());
    }

    protected function invalidInjection(InvalidClass $class)
    {
    }
}
