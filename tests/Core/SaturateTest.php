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
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Core\Tests\Fixtures\TestConfig;
use Spiral\Core\Traits\SaturateTrait;

class SaturateTest extends TestCase
{
    use SaturateTrait;

    public function testSaturate(): void
    {
        $value = $this->saturate(new TestConfig(), TestConfig::class);
        $this->assertInstanceOf(TestConfig::class, $value);
    }

    public function testSaturateFromContainer(): void
    {
        $c = new Container();
        $c->bind(TestConfig::class, new TestConfig());

        ContainerScope::runScope($c, function (): void {
            $value = $this->saturate(null, TestConfig::class);
            $this->assertInstanceOf(TestConfig::class, $value);
        });
    }

    /**
     * @expectedException \Spiral\Core\Exception\ScopeException
     */
    public function testSaturateException(): void
    {
        $value = $this->saturate(null, TestConfig::class);
    }

    /**
     * @expectedException \Spiral\Core\Exception\ScopeException
     */
    public function testSaturateExceptionBinding(): void
    {
        ContainerScope::runScope(new Container(), function (): void {
            $value = $this->saturate(null, TestConfig::class);
            $this->assertInstanceOf(TestConfig::class, $value);
        });
    }
}
