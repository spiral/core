<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests;


use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Core\Tests\Fixtures\TestConfig;
use Spiral\Core\Traits\SaturateTrait;

class SaturateTest extends TestCase
{
    use SaturateTrait;

    public function testSaturate()
    {
        $value = $this->saturate(new TestConfig(), TestConfig::class);
        $this->assertInstanceOf(TestConfig::class, $value);
    }

    public function testSaturateFromContainer()
    {
        $c = new Container();
        $c->bind(TestConfig::class, new TestConfig());

        ContainerScope::runScope($c, function () {
            $value = $this->saturate(null, TestConfig::class);
            $this->assertInstanceOf(TestConfig::class, $value);
        });
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ScopeException
     */
    public function testSaturateException()
    {
        $value = $this->saturate(null, TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ScopeException
     */
    public function testSaturateExceptionBinding()
    {
        ContainerScope::runScope(new Container(), function () {
            $value = $this->saturate(null, TestConfig::class);
            $this->assertInstanceOf(TestConfig::class, $value);
        });
    }
}