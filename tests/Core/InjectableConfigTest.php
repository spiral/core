<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\InjectableConfig;

class InjectableConfigTest extends TestCase
{
    public function testArrayAccess()
    {
        $config = new InjectableConfig([
            'key' => 'value',
        ]);

        $this->assertArrayHasKey('key', $config);
        $this->assertEquals('value', $config['key']);

        $this->assertArrayNotHasKey('otherKey', $config);
    }

    public function testToArray()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $this->assertEquals([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ], $config->toArray());
    }

    public function testIteration()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $iterator = $config->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertSame($iterator->getArrayCopy(), $config->toArray());
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ConfigException
     * @expectedExceptionMessage Unable to change configuration data, configs are treated as
     *                           immutable by default
     */
    public function testWriteError()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $config['keyA'] = 'abc';
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ConfigException
     * @expectedExceptionMessage Unable to change configuration data, configs are treated as
     *                           immutable by default
     */
    public function testUnsetError()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        unset($config['keyA']);
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ConfigException
     * @expectedExceptionMessage Undefined configuration key 'keyC'
     */
    public function testGetError()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $config['keyC'];
    }

    /**
     * @covers \Spiral\Core\InjectableConfig::__set_state()
     */
    public function testSerialize()
    {
        $config = new InjectableConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $serialized = serialize($config);
        $this->assertEquals($config, unserialize($serialized));

        $this->assertEquals($config, InjectableConfig::__set_state([
            'config' => [
                'keyA' => 'value',
                'keyB' => 'valueB',
            ]
        ]));
    }
}
