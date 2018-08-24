<?php
/**
 * components
 *
 * @author    Wolfy-J
 */

namespace Spiral\Core\Tests;

use Interop\Container\ContainerInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Core\Tests\Fixtures\SampleClass;

class DelegateTest extends TestCase
{
    public function testHasDelegate()
    {
        $outer = m::mock(ContainerInterface::class);
        $outer->shouldReceive('has')->with('abc')->andReturn(true);

        $container = new Container($outer);

        $this->assertTrue($container->has('abc'));
    }

    public function testGetDelegate()
    {
        $outer = m::mock(ContainerInterface::class);
        $outer->shouldReceive('has')->with('abc')->andReturn(true);
        $outer->shouldReceive('get')->with('abc')->andReturn(new SampleClass());

        $container = new Container($outer);

        $this->assertInstanceOf(SampleClass::class, $container->get('abc'));
    }
}