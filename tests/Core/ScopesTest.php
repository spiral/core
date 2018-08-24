<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Core\Exceptions\RuntimeException;
use Spiral\Core\Tests\Fixtures\Bucket;
use Spiral\Core\Tests\Fixtures\SampleClass;
use Spiral\Core\Traits\ScopeTrait;

class ScopesTest extends TestCase
{
    use ScopeTrait;

    public function testScope()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        $this->assertTrue(ContainerScope::globalScope($container, function () use ($container) {
            return $container === ContainerScope::getContainer();
        }));

        $this->assertNull(ContainerScope::getContainer());
    }

    public function testScopeTrait()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        $this->assertTrue(ContainerScope::globalScope($container, function () {
            return $this->iocContainer() === ContainerScope::getContainer();
        }));

        $this->assertNull(ContainerScope::getContainer());
    }

    public function testScopeException()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->assertNull(ContainerScope::getContainer());

        try {
            $this->assertTrue(ContainerScope::globalScope($container, function () use ($container) {
                throw new RuntimeException("exception");
            }));
        } catch (\Throwable $e) {

        }

        $this->assertInstanceOf(RuntimeException::class, $e);
        $this->assertNull(ContainerScope::getContainer());
    }

    public function testContainerScope()
    {
        $c = new Container();
        $c->bind('bucket', new Bucket("a"));

        $this->assertSame("a", $c->get("bucket")->getName());
        $this->assertFalse($c->has("other"));

        $this->assertTrue($c->runScope([
            'bucket' => new Bucket('b'),
            'other'  => new SampleClass()
        ], function () use ($c) {
            $this->assertSame("b", $c->get("bucket")->getName());
            $this->assertTrue($c->has("other"));

            return $c->get('bucket')->getName() == "b" && $c->has("other");
        }));

        $this->assertSame("a", $c->get("bucket")->getName());
        $this->assertFalse($c->has("other"));
    }

    public function testContainerScopeException()
    {
        $c = new Container();
        $c->bind('bucket', new Bucket("a"));

        $this->assertSame("a", $c->get("bucket")->getName());
        $this->assertFalse($c->has("other"));

        $this->assertTrue($c->runScope([
            'bucket' => new Bucket('b'),
            'other'  => new SampleClass()
        ], function () use ($c) {
            $this->assertSame("b", $c->get("bucket")->getName());
            $this->assertTrue($c->has("other"));

            return $c->get('bucket')->getName() == "b" && $c->has("other");
        }));

        try {
            $this->assertTrue($c->runScope([
                'bucket' => new Bucket('b'),
                'other'  => new SampleClass()
            ], function () use ($c) {
                throw new RuntimeException("exception");
            }));
        } catch (\Throwable $e) {

        }

        $this->assertSame("a", $c->get("bucket")->getName());
        $this->assertFalse($c->has("other"));
    }
}