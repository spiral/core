<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests\Fixtures;

use Spiral\Core\InjectableConfig;
use Spiral\Core\Traits\Config\AliasTrait;

class TestConfig extends InjectableConfig
{
    use AliasTrait;
}
