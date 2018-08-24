<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Exceptions;

/**
 * Raised in cases when "sugar" code can not be executed (for example there is no shared/static
 * container but developer's code is using shorted code version).
 *
 * Sugar conditions are avoidable.
 */
class ScopeException extends LogicException
{
}
