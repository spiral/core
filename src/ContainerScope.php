<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerInterface;

/**
 * Scope class provides ability to enable or disable global container access within specific access scope.
 */
final class ContainerScope
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Returns currently active container scope if any.
     *
     * @return null|ContainerInterface
     */
    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }

    /**
     * Invokes given closure or function withing global IoC scope.
     *
     * @param ContainerInterface $container
     * @param callable           $scope
     * @return mixed
     * @throws \Throwable
     */
    public static function runGlobal(ContainerInterface $container, callable $scope)
    {
        list ($previous, self::$container) = [self::$container, $container];
        try {
            return $scope();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            self::$container = $previous;
        }
    }
}