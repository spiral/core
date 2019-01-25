<?php
declare(strict_types=1);/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Exception\Container;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Exception\DependencyException;

/**
 * Something inside container.
 */
class ContainerException extends DependencyException implements ContainerExceptionInterface
{
}
