<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\Tests\Fixtures\BootloaderA;
use Spiral\Core\Tests\Fixtures\BootloaderB;

class DependenciesTest extends TestCase
{
    public function testDep()
    {
        $c = new Container();

        $b = new BootloadManager($c);

        $b->bootload([BootloaderA::class]);

        $this->assertTrue($c->has('a'));
        $this->assertFalse($c->has('b'));
    }

    public function testDep2()
    {
        $c = new Container();

        $b = new BootloadManager($c);

        $b->bootload([BootloaderB::class]);

        $this->assertTrue($c->has('a'));
        $this->assertTrue($c->has('b'));
    }
}