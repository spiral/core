<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\MemoryInterface;
use Spiral\Core\ProcessMemory;

class ProcessMemoryTest extends TestCase
{
    public function testSaveLoadData()
    {
        $memory = new ProcessMemory();
        $this->assertInstanceOf(MemoryInterface::class, $memory);
        $this->assertNull($memory->loadData('test'));

        $memory->saveData("test", "value");
        $this->assertSame("value", $memory->loadData('test'));
    }
}