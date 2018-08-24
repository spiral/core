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
use Spiral\Core\Scope;

class ScopeTest extends TestCase
{
    public function testScope()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(Scope::getContainer());

        $this->assertTrue(Scope::run($container, function () use ($container) {
            return $container === Scope::getContainer();
        }));

        $this->assertNull(Scope::getContainer());
    }
}