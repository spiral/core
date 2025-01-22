<?php

declare(strict_types=1);

namespace Spiral\Tests\Core\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionFunction;
use Spiral\Core\Container;
use Spiral\Core\Exception\Traits\ClosureRendererTrait;
use stdClass;

class ClosureRendererTraitTest extends TestCase
{
    use ClosureRendererTrait;

    private const TEST_CONSTANT = 'foo';

    public function testStaticFnWithoutParams(): void
    {
        $this->assertSame(
            'static function ()',
            $this->renderClosureForTesting(static fn () => null),
        );
    }

    public function testNonStaticFnWithoutParams(): void
    {
        $this->assertSame(
            'function ()',
            $this->renderClosureForTesting(fn () => null),
        );
    }

    public function testMixedParams(): void
    {
        $this->assertSame(
            'function (mixed $mixed, $noType)',
            $this->renderClosureForTesting(fn (mixed $mixed, $noType) => null),
        );
    }

    public function testNullableTypes(): void
    {
        $this->assertSame(
            'function (?int $a, ?string $b, ?float $c, ?bool $d, ?callable $e)',
            $this->renderClosureForTesting(fn (?int $a, ?string $b, ?float $c, ?bool $d, ?callable $e) => null),
        );
    }

    public function testVariadicAndReference1(): void
    {
        $this->assertSame(
            'function (?int &$a, &$b, ...$e)',
            $this->renderClosureForTesting(fn (?int &$a, &$b, ...$e) => null),
        );
    }

    public function testVariadicAndReference2(): void
    {
        $this->assertSame(
            'function (int &...$v)',
            $this->renderClosureForTesting(fn (int &...$v) => null),
        );
    }

    public function testVariadic(): void
    {
        $this->assertSame(
            'function (int ...$v)',
            $this->renderClosureForTesting(fn (int ...$v) => null),
        );
    }

    public function testClass(): void
    {
        $this->assertSame(
            'function (stdClass ...$v)',
            $this->renderClosureForTesting(fn (stdClass ...$v) => null),
        );
    }

    public function testSelfType(): void
    {
        $this->assertSame(
            'function (self $v)',
            $this->renderClosureForTesting(fn (self $v) => null),
        );
    }

    public function testClassWithNamespace(): void
    {
        $this->assertSame(
            'function (?' . Container::class . ' ...$v)',
            $this->renderClosureForTesting(fn (?Container ...$v) => null),
        );
    }

    public function testUnionTypes(): void
    {
        $this->assertSame(
            'function (self|string|int|null $v)',
            $this->renderClosureForTesting(fn (self|string|int|null $v) => null),
        );
    }

    public function testTypeIntersection(): void
    {
        $this->assertSame(
            'function (' . ContainerInterface::class . '&' . ContainerExceptionInterface::class . ' $v)',
            $this->renderClosureForTesting(fn (ContainerInterface&ContainerExceptionInterface $v) => null),
        );
    }

    public function testFunctionFromEval(): void
    {
        eval('$fn = fn (string $v) => null;');
        $this->assertSame(
            'function (string $v)',
            $this->renderClosureForTesting($fn),
        );
    }

    public function testUnavailableClasses(): void
    {
        $this->assertSame(
            'function (Foo|Bar $v)',
            $this->renderClosureForTesting(fn (\Foo|\Bar $v) => null),
        );
    }

    public function testDefaultObjectValue(): void
    {
        $this->assertSame(
            'function (object $v = new stdClass(...))',
            $this->renderClosureForTesting(fn (object $v = new stdClass(['foo' => 'bar'])) => null),
        );
    }

    public function testDefaultScalarValues(): void
    {
        $this->assertSame(
            'function (?string $a = NULL, string $b = \'test\', $i = 5, $c = self::TEST_CONSTANT)',
            $this->renderClosureForTesting(
                fn (?string $a = null, string $b = "test", $i = 5, $c = self::TEST_CONSTANT) => null
            ),
        );
    }

    /**
     * @requires PHP >= 8.2
     *
     * @link https://wiki.php.net/rfc/null-false-standalone-types
     */
    public function testNullAndFalseTypes(): void
    {
        eval('$fn = fn (null $a, false $b) => null;');
        $this->assertSame(
            'function (null $a, false $b)',
            $this->renderClosureForTesting($fn),
        );
    }

    private function renderClosureForTesting(\Closure $closure): string
    {
        return $this->renderClosureSignature(new ReflectionFunction($closure));
    }
}
