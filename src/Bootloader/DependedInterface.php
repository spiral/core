<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Bootloader;

/**
 * Declares that bootloader has other bootloaders as dependencies.
 */
interface DependedInterface
{
    /**
     * Return class names of bootloders current bootloader depends on.
     * Bootloaders will be initiated automatically.
     *
     * @return array
     */
    public function defineDependencies(): array;
}