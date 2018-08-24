<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Spiral\Core;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Spiral\Core\ContainerScope;
use Spiral\Core\Exceptions\RuntimeException;
use Spiral\Core\Traits\ScopeTrait;

class ContainerScopeTest extends TestCase
{
    use ScopeTrait;

    public function testScope()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        $this->assertTrue(ContainerScope::run($container, function () use ($container) {
            return $container === ContainerScope::getContainer();
        }));

        $this->assertNull(ContainerScope::getContainer());
    }

    public function testScopeTrait()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        $this->assertTrue(ContainerScope::run($container, function () {
            return $this->iocContainer() === ContainerScope::getContainer();
        }));

        $this->assertNull(ContainerScope::getContainer());
    }

    public function testScopeException()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        try {
            $this->assertTrue(ContainerScope::run($container, function () use ($container) {
                throw new RuntimeException("exception");
            }));
        } catch (\Throwable $e) {

        }

        $this->assertInstanceOf(RuntimeException::class, $e);
        $this->assertNull(ContainerScope::getContainer());
    }
}