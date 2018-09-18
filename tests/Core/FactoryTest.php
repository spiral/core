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
use Spiral\Core\FactoryInterface;
use Spiral\Core\Tests\Fixtures\BadClass;
use Spiral\Core\Tests\Fixtures\Bucket;
use Spiral\Core\Tests\Fixtures\CorruptedClass;
use Spiral\Core\Tests\Fixtures\SampleClass;

class FactoryTest extends TestCase
{
    public function testAutoFactory()
    {
        $container = new Container();
        $this->assertInstanceOf(FactoryInterface::class, $container);

        $bucket = $container->make(Bucket::class, [
            'name' => 'abc',
            'data' => 'some data',
        ]);

        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertSame('abc', $bucket->getName());
        $this->assertSame('some data', $bucket->getData());
    }

    public function testClosureFactory()
    {
        $container = new Container();
        $this->assertInstanceOf(FactoryInterface::class, $container);

        $container->bind(Bucket::class, function ($data) {
            return new Bucket('via-closure', $data);
        });

        $bucket = $container->make(Bucket::class, [
            'data' => 'some data',
        ]);

        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertSame('via-closure', $bucket->getName());
        $this->assertSame('some data', $bucket->getData());
    }

    public function testMethodFactory()
    {
        $container = new Container();
        $this->assertInstanceOf(FactoryInterface::class, $container);

        $container->bind(Bucket::class, [self::class, 'makeBucket']);

        $bucket = $container->make(Bucket::class, [
            'data' => 'some data',
        ]);

        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertSame('via-method', $bucket->getName());
        $this->assertSame('some data', $bucket->getData());
    }

    public function testCascadeFactory()
    {
        $container = new Container();
        $this->assertInstanceOf(FactoryInterface::class, $container);

        $sample = new SampleClass();

        $container->bind(Bucket::class, [self::class, 'makeBucketWithSample']);
        $container->bind(SampleClass::class, function () use ($sample) {
            return $sample;
        });

        $bucket = $container->make(Bucket::class);

        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertSame('via-method-with-sample', $bucket->getName());
        $this->assertSame($sample, $bucket->getData());
    }

    /**
     * @expectedException \Spiral\Core\Exception\Container\ContainerException
     */
    public function testConstructAbstract()
    {
        $container = new Container();
        $container->make(BadClass::class);
    }

    /**
     * @expectedException \ParseError
     */
    public function testConstructCorrupted()
    {
        $container = new Container();
        $container->make(CorruptedClass::class);
    }

    /**
     * @param mixed $data
     *
     * @return Bucket
     */
    private function makeBucket($data)
    {
        return new Bucket('via-method', $data);
    }

    /**
     * @param SampleClass $sample
     *
     * @return Bucket
     */
    private function makeBucketWithSample(SampleClass $sample)
    {
        return new Bucket('via-method-with-sample', $sample);
    }
}
