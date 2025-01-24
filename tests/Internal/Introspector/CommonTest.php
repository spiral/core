<?php

declare(strict_types=1);

namespace Spiral\Tests\Core\Internal\Introspector;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Spiral\Core\Attribute\Proxy;
use Spiral\Core\Container;
use Spiral\Core\Internal\Introspector;
use Spiral\Core\Scope;

final class CommonTest extends TestCase
{
    public function testScopeName(): void
    {
        $container = new Container();

        self::assertSame('root', Introspector::scopeName($container));

        $container->invoke(static fn() => self::assertSame('root', Introspector::scopeName()));

        $container->runScope(new Scope('test'), static function (ContainerInterface $container): void {
            self::assertSame('test', Introspector::scopeName($container));
            self::assertSame('test', Introspector::scopeName());
        });
    }

    public function testScopeNames(): void
    {
        $container = new Container();

        $container->runScope(new Scope('test'), static function (Container $c): void {
            $c->runScope(new Scope(), static function (Container $c): void {
                $c->runScope(new Scope('bar'), static function (Container $c): void {
                    self::assertSame(['bar', null, 'test', 'root'], Introspector::scopeNames($c));
                    self::assertSame(['bar', null, 'test', 'root'], Introspector::scopeNames());
                });
            });
        });
    }

    public function testProxyContainer(): void
    {
        $container = new Container();

        $container->runScope(new Scope('test'), static function (Container $c): void {
            $c->runScope(new Scope(), static function (Container $c): void {
                $c->runScope(new Scope('bar'), static function (Container $c, #[Proxy] ContainerInterface $proxy): void {
                    self::assertSame(['bar', null, 'test', 'root'], Introspector::scopeNames($proxy));
                });
            });
        });
    }
}
