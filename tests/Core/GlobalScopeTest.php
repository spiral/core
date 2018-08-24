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

class GlobalScopeTest extends TestCase
{
    public function testScope()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        $this->assertTrue(ContainerScope::run($container, function () use ($container) {
            return $container === ContainerScope::getContainer();
        }));

        $this->assertNull(ContainerScope::getContainer());
    }
}