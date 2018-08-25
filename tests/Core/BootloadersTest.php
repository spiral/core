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
use Spiral\Core\MemoryInterface;
use Spiral\Core\ProcessMemory;
use Spiral\Core\Tests\Fixtures\SampleBoot;
use Spiral\Core\Tests\Fixtures\SampleClass;

class BootloadersTest extends TestCase
{
    public function testSchemaLoading()
    {
        $container = new Container();
        $memory = new ProcessMemory();

        $bootloader = new BootloadManager($container, $memory);

        $this->assertEmpty($memory->loadData('sample-load'));
        $bootloader->bootload([SampleClass::class], 'sample-load');
        $this->assertNotEmpty($memory->loadData('sample-load'));

        $memory = $this->createMock(MemoryInterface::class);
        $memory->method('loadData')->withConsecutive(['sample-load'])->willReturn([
            'snapshot'    => [
                0 => 'Spiral\\Tests\\Core\\Fixtures\\SampleClass',
                1 => 'Spiral\\Tests\\Core\\Fixtures\\SampleBoot',
            ],
            'bootloaders' => [
                'Spiral\\Tests\\Core\\Fixtures\\SampleClass' => ['init' => true, 'boot' => false,],
                'Spiral\\Tests\\Core\\Fixtures\\SampleBoot'  => [
                    'init'       => false,
                    'boot'       => false,
                    'bindings'   => ['abc' => 'Spiral\\Tests\\Core\\Fixtures\\SampleBoot',],
                    'singletons' => [],
                ],
            ],
        ]);

        $bootloader = new BootloadManager($container, $memory);
        $bootloader->bootload([SampleClass::class, SampleBoot::class], 'sample-load');

        $this->assertTrue($container->has('abc'));
    }
}