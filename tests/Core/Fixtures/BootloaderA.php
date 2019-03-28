<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests\Fixtures;


use Spiral\Core\Bootloader\Bootloader;

class BootloaderA extends Bootloader
{
    public const BINDINGS = [
        'a' => true
    ];
}