<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Tests\Fixtures\TestConfig;
use Spiral\Core\Traits\Config\AliasTrait;

class InjectableConfigTest extends TestCase
{
    use AliasTrait;

    protected $config = [
        'aliases' => [
            'default' => 'value',
            'value'   => 'another',
            'another' => 'test'
        ]
    ];

    public function testArrayAccess()
    {
        $config = new TestConfig([
            'key' => 'value',
        ]);

        $this->assertArrayHasKey('key', $config);
        $this->assertEquals('value', $config['key']);

        $this->assertArrayNotHasKey('otherKey', $config);
    }

    public function testToArray()
    {
        $config = new TestConfig([
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
        $config = new TestConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $iterator = $config->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertSame($iterator->getArrayCopy(), $config->toArray());
    }

    /**
     * @expectedException \Spiral\Core\Exception\ConfigException
     * @expectedExceptionMessage Unable to change configuration data, configs are treated as
     *                           immutable by default
     */
    public function testWriteError()
    {
        $config = new TestConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $config['keyA'] = 'abc';
    }

    /**
     * @expectedException \Spiral\Core\Exception\ConfigException
     * @expectedExceptionMessage Unable to change configuration data, configs are treated as
     *                           immutable by default
     */
    public function testUnsetError()
    {
        $config = new TestConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        unset($config['keyA']);
    }

    /**
     * @expectedException \Spiral\Core\Exception\ConfigException
     * @expectedExceptionMessage Undefined configuration key 'keyC'
     */
    public function testGetError()
    {
        $config = new TestConfig([
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
        $config = new TestConfig([
            'keyA' => 'value',
            'keyB' => 'valueB',
        ]);

        $serialized = serialize($config);
        $this->assertEquals($config, unserialize($serialized));

        $this->assertEquals($config, TestConfig::__set_state([
            'config' => [
                'keyA' => 'value',
                'keyB' => 'valueB',
            ]
        ]));
    }

    public function testAliases()
    {
        $this->assertEquals('test', $this->resolveAlias('default'));
        $this->assertEquals('test', $this->resolveAlias('value'));
    }
}
