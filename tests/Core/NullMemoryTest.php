<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Spiral\Core;

use PHPUnit\Framework\TestCase;
use Spiral\Core\MemoryInterface;
use Spiral\Core\NullMemory;

class NullMemoryTest extends TestCase
{
    public function testLoadData()
    {
        $memory = new NullMemory();
        $this->assertInstanceOf(MemoryInterface::class, $memory);
        $this->assertNull($memory->loadData('test'));
    }

    public function testSaveData()
    {
        $memory = new NullMemory();
        $this->assertInstanceOf(MemoryInterface::class, $memory);
        $memory->saveData('test', null);
    }
}