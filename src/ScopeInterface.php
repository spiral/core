<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

/**
 * Provides ability to run code withing isolated IoC scope.
 */
interface ScopeInterface
{
    /**
     * Invokes given closure or function withing specific IoC scope.
     *
     * Example:
     *
     * $container->run(['actor' => new Actor()], function() use($container) {
     *    dump($container->get('actor'));
     * });
     *
     * @param array    $bindings
     * @param callable $scope
     * @return mixed
     * @throws \Throwable
     */
    public function run(array $bindings, callable $scope);
}