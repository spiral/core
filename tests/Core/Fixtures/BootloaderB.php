<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests\Fixtures;

use Spiral\Core\Bootloader\Bootloader;
use Spiral\Core\Bootloader\DependedInterface;

class BootloaderB extends Bootloader implements DependedInterface
{
    public const BINDINGS = [
        'b' => true
    ];

    public function defineDependencies(): array
    {
        return [BootloaderA::class];
    }
}