<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Traits\Config;

/**
 * Provides aliasing ability for config classes.
 */
trait AliasTrait
{
    /**
     * @param string $alias
     *
     * @return string
     */
    public function resolveAlias(string $alias): string
    {
        while (is_string($alias) && isset($this->config) && isset($this->config['aliases'][$alias])) {
            $alias = $this->config['aliases'][$alias];
        }

        return $alias;
    }
}
