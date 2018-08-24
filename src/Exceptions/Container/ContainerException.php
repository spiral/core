<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Exceptions\Container;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Exceptions\DependencyException;

/**
 * Something inside container.
 */
class ContainerException extends DependencyException implements ContainerExceptionInterface
{
}
