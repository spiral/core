<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Traits;

use Psr\Container\ContainerInterface;
use Spiral\Core\ContainerScope;

/**
 * Trait provides access to global container scope if any.
 */
trait ScopeTrait
{
    /**
     * Returns currently active container scope if any.
     *
     * @return null|ContainerInterface
     */
    protected function iocContainer(): ?ContainerInterface
    {
        return ContainerScope::getContainer();
    }
}