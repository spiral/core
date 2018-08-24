<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Core\Exceptions\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AutowireException implements NotFoundExceptionInterface
{

}