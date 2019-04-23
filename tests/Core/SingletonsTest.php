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
use Spiral\Core\Tests\Fixtures\DeclarativeSingleton;
use Spiral\Core\Tests\Fixtures\SampleClass;

class SingletonsTest extends TestCase
{
    public function testSingletonInstance()
    {
        $container = new Container();
        $container->bindSingleton('sampleClass', $instance = new SampleClass());
        $this->assertSame($instance, $container->get('sampleClass'));
    }

    public function testSingletonToItself()
    {
        $container = new Container();
        $container->bindSingleton(SampleClass::class, SampleClass::class);

        $sc = $container->get(SampleClass::class);
        $this->assertTrue($container->hasInstance(SampleClass::class));
        $this->assertSame($sc, $container->get(SampleClass::class));
    }

    public function testSingletonInstanceWithBinding()
    {
        $container = new Container();
        $container->bindSingleton('sampleClass', $instance = new SampleClass());
        $container->bind('binding', 'sampleClass');

        $this->assertSame($instance, $container->get('sampleClass'));
        $this->assertSame($instance, $container->get('binding'));
    }

    public function testHasInstance()
    {
        $container = new Container();
        $container->bindSingleton('sampleClass', $instance = new SampleClass());

        $this->assertTrue($container->hasInstance('sampleClass'));
        $this->assertFalse($container->hasInstance('otherClass'));
    }

    public function testSingletonClosure()
    {
        $container = new Container();

        $instance = new SampleClass();

        $container->bindSingleton('sampleClass', function () use ($instance) {
            return $instance;
        });

        $this->assertSame($instance, $container->get('sampleClass'));
    }

    public function testSingletonClosureTwice()
    {
        $container = new Container();

        $container->bindSingleton('sampleClass', function () {
            return new SampleClass();
        });

        $instance = $container->get('sampleClass');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('sampleClass'));
    }

    public function testSingletonFactory()
    {
        $container = new Container();

        $container->bindSingleton('sampleClass', [self::class, 'sampleClass']);

        $instance = $container->get('sampleClass');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('sampleClass'));
    }

    public function testDelayedSingleton()
    {
        $container = new Container();
        $container->bindSingleton('singleton', 'sampleClass');

        $container->bind('sampleClass', function () {
            return new SampleClass();
        });

        $instance = $container->get('singleton');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('singleton'));
        $this->assertNotSame($instance, $container->get('sampleClass'));
    }

    public function testDeclarativeSingleton()
    {
        $container = new Container();

        $instance = $container->get(DeclarativeSingleton::class);

        $this->assertInstanceOf(DeclarativeSingleton::class, $instance);
        $this->assertSame($instance, $container->get(DeclarativeSingleton::class));
    }

    /**
     * @return SampleClass
     */
    private function sampleClass()
    {
        return new SampleClass();
    }
}
