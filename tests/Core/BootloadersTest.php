<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\Tests\Fixtures\SampleBoot;
use Spiral\Core\Tests\Fixtures\SampleClass;

class BootloadersTest extends TestCase
{
    public function testSchemaLoading()
    {
        $container = new Container();

        $bootloader = new BootloadManager($container);
        $bootloader->bootload([SampleClass::class, SampleBoot::class]);

        $this->assertTrue($container->has('abc'));
        $this->assertTrue($container->hasInstance('cde'));
        $this->assertTrue($container->has('single'));

        $this->assertSame([SampleClass::class, SampleBoot::class], $bootloader->getClasses());
    }

    /**
     * @expectedException \Spiral\Core\Exception\BootloadException
     */
    public function testException()
    {
        $container = new Container();

        $bootloader = new BootloadManager($container);
        $bootloader->bootload(['Invalid']);
    }
}