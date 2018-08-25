<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Tests\Fixtures;

use Spiral\Core\BinderInterface;
use Spiral\Core\Bootloaders\Bootloader;

class SampleBoot extends Bootloader
{
    const BOOT = true;

    const BINDINGS = ['abc' => self::class];

    public function boot(BinderInterface $binder)
    {
        $binder->bind('cde', new SampleClass());
    }
}