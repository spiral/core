<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Core\Traits;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\ContainerScope;
use Spiral\Core\Exception\ScopeException;

/**
 * Saturate optional constructor or method argument (class) using internal (usually static)
 * container. In most of cases trait is doing nothing since spiral Container populates even
 * optional class dependencies.
 *
 * @internal
 */
trait SaturateTrait
{
    /**
     * Must be used only to resolve optional constructor arguments. Use in classes which are
     * generally resolved using Container. Default value MUST always be supplied from outside.
     *
     * @param mixed  $default Default value.
     * @param string $class   Requested class.
     * @return mixed
     *
     * @throws ScopeException
     */
    private function saturate($default, string $class)
    {
        if (!empty($default)) {
            return $default;
        }

        $container = ContainerScope::getContainer();
        if (empty($container)) {
            throw new ScopeException("Unable to saturate '{$class}': no container available");
        }

        try {
            return $container->get($class);
        } catch (ContainerExceptionInterface $e) {
            throw new ScopeException("Unable to saturate '{$class}': {$e->getMessage()}", 0, $e);
        }
    }
}
