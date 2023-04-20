<?php

declare(strict_types=1);

namespace Spiral\Tests\Core\Scope;

use Spiral\Core\Container;
use Spiral\Core\Exception\Scope\FinalizersException;
use Spiral\Tests\Core\Scope\Stub\AttrFinalize;
use Spiral\Tests\Core\Scope\Stub\AttrScopeFooFinalize;
use Spiral\Tests\Core\Scope\Stub\FileLogger;
use Spiral\Tests\Core\Scope\Stub\LoggerInterface;

final class FinalizeAttributeTest extends BaseTest
{
    /**
     * Finalizer from a attribute should be registered and called when a related scope is destroyed.
     */
    public function testFinalizerHasBeenRegisteredAndRun(): void
    {
        $root = new Container();

        $obj = $root->scope(static function (Container $c1) {
            $obj = $c1->scope(static function (Container $c2) {
                $obj = $c2->get(AttrScopeFooFinalize::class);

                self::assertFalse($obj->finalized);
                return $obj;
            });

            self::assertFalse($obj->finalized);
            return $obj;
        }, name: 'foo');

        // Finalizer should be called when the scope `foo` is destroyed.
        self::assertTrue($obj->finalized);
    }

    /**
     * Finalizer should be autowired.
     */
    public function testFinalizerAutowiringOnCall(): void
    {
        $root = new Container();
        $root->bindSingleton(LoggerInterface::class, FileLogger::class);

        $obj2 = null;
        $obj = $root->scope(static function (Container $c1) use (&$obj2) {
            $obj = $c1->scope(static function (Container $c2) use (&$obj2) {
                $obj = $c2->get(AttrScopeFooFinalize::class);
                $obj2 = $c2->get(AttrScopeFooFinalize::class);

                self::assertNotSame($obj, $obj2);
                self::assertNull($obj->logger);
                self::assertNull($obj2->logger);
                return $obj;
            });

            self::assertNull($obj2->logger);
            self::assertNull($obj->logger);
            return $obj;
        }, name: 'foo');


        self::assertInstanceOf(FileLogger::class, $obj2->logger);
        self::assertInstanceOf(FileLogger::class, $obj->logger);
        self::assertSame($obj2->logger, $obj->logger);
    }

    /**
     * Finalizer without any scope constraint should be called when its scope is destroyed.
     */
    public function testFinalizerWithoutConcreteScope(): void
    {
        $root = new Container();
        $root->bindSingleton(LoggerInterface::class, FileLogger::class);

        $obj2 = null;
        $obj = $root->scope(static function (Container $c1) use (&$obj2) {
            $obj = $c1->scope(static function (Container $c2) use (&$obj2) {
                $obj = $c2->get(AttrFinalize::class);
                $obj2 = $c2->get(AttrFinalize::class);

                self::assertNotSame($obj, $obj2);
                self::assertNull($obj->logger);
                self::assertNull($obj2->logger);
                return $obj;
            });

            self::assertNull($obj2->logger);
            self::assertNull($obj->logger);
            return $obj;
        }, bindings: [AttrFinalize::class => AttrFinalize::class], name: 'foo');


        self::assertInstanceOf(FileLogger::class, $obj2->logger);
        self::assertInstanceOf(FileLogger::class, $obj->logger);
        self::assertSame($obj2->logger, $obj->logger);
    }

    /**
     * Finalizer without any scope constraint should be called when its scope is destroyed even if the scope is root.
     */
    public function testFinalizerWithoutConcreteScopeInRoot(): void
    {
        $root = new Container();
        $root->bindSingleton(LoggerInterface::class, FileLogger::class);

        $obj = $root->get(AttrFinalize::class);

        self::assertNull($obj->logger);

        // Destroy the root scope.
        unset($root);
        self::assertInstanceOf(LoggerInterface::class, $obj->logger);
    }

    public function testExceptionOnDestroy()
    {
        $root = new Container();

        self::expectException(FinalizersException::class);
        self::expectExceptionMessage('An exception has been thrown during finalization of the scope `foo`');

        try {
            $root->scope(static function (Container $c1) {
                $obj = $c1->get(AttrScopeFooFinalize::class);
                $obj->throwException = true;
            }, name: 'foo');
        } catch (FinalizersException $e) {
            self::assertSame('foo', $e->getScope());
            self::assertCount(1, $e->getExceptions());
            // Contains the message from the inner exception.
            self::assertStringContainsString(
                'Test exception from finalize method',
                $e->getMessage(),
            );
            self::assertStringContainsString(
                'Test exception from finalize method',
                $e->getExceptions()[0]->getMessage(),
            );
            throw $e;
        }
    }

    public function testManyExceptionsOnDestroy()
    {
        $root = new Container();

        self::expectException(FinalizersException::class);
        self::expectExceptionMessage('3 exceptions have been thrown during finalization of the scope `foo`');

        try {
            $root->scope(static function (Container $c1) {
                $c1->get(AttrScopeFooFinalize::class)->throwException = true;
                $c1->get(AttrScopeFooFinalize::class)->throwException = true;
                $c1->get(AttrScopeFooFinalize::class)->throwException = true;
            }, name: 'foo');
        } catch (FinalizersException $e) {
            self::assertSame('foo', $e->getScope());
            self::assertCount(3, $e->getExceptions());
            // Contains the message from the inner exception.
            self::assertStringContainsString(
                'Test exception from finalize method',
                $e->getMessage(),
            );
            throw $e;
        }
    }
}