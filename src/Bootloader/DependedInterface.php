<?php
declare(strict_types=1);/**
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
     * Related bootloaders will be initiated automatically with nested
     * dependencies.
     *
     * @return array
     */
    public function defineDependencies(): array;
}