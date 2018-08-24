<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Core\Exceptions\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Caused when container is not able to find the proper binding.
 */
class NotFoundException extends AutowireException implements NotFoundExceptionInterface
{

}