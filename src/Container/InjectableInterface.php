<?php
declare(strict_types=1);/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Container;

/**
 * Must define constant INJECTOR pointing to associated injector class or binding.
 *
 * Attention, this abstraction is currently under re-thinking process in order to replace it with
 * binded context-specific factory (non breaking change).
 */
interface InjectableInterface
{
}
