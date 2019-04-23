<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Core\Traits;

use Psr\Container\ContainerInterface;
use Spiral\Core\ContainerScope;

/**
 * Trait provides access to global container scope if any.
 *
 * @internal
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