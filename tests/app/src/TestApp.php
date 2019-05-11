<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\App;

use Spiral\App\Bootloader\AppBootloader;
use Spiral\Boot\BootloadManager;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Bootloader;
use Spiral\Console\Console;
use Spiral\Core\Kernel;

class TestApp extends Kernel
{
    const LOAD = [
        // Core Services
        Bootloader\DebugBootloader::class,
        Bootloader\SnapshotsBootloader::class,
        Bootloader\I18nBootloader::class,

        // Validation, filtration, security
        Bootloader\Security\EncrypterBootloader::class,
        Bootloader\Security\ValidationBootloader::class,
        Bootloader\Security\FiltersBootloader::class,
        Bootloader\Security\RbacBootloader::class,

        // Dispatchers
        Bootloader\Jobs\JobsBootloader::class,
        Bootloader\ConsoleBootloader::class,

        // HTTP extensions
        Bootloader\Http\MvcBootloader::class,
        Bootloader\Http\ErrorHandlerBootloader::class,
        Bootloader\Http\SessionBootloader::class,
        Bootloader\Http\CookiesBootloader::class,
        Bootloader\Http\CsrfBootloader::class,

        // Data and Storage
        Bootloader\Database\DatabaseBootloader::class,
        Bootloader\Database\MigrationsBootloader::class,
        Bootloader\Http\PaginationBootloader::class,
        Bootloader\Cycle\CycleBootloader::class,

        // Template engines and rendering
        Bootloader\Views\ViewsBootloader::class,
        Bootloader\Views\TranslatedCacheBootloader::class,

        // Framework commands
        Bootloader\CommandBootloader::class
    ];

    const APP = [AppBootloader::class];

    /**
     * @param string $alias
     * @return mixed|null|object
     */
    public function get(string $alias)
    {
        return $this->container->get($alias);
    }

    /**
     * @param string $alias
     * @return string
     */
    public function dir(string $alias): string
    {
        return $this->container->get(DirectoriesInterface::class)->get($alias);
    }

    /**
     * @return Console
     */
    public function console(): Console
    {
        return $this->get(Console::class);
    }

    public function getBootloadManager(): BootloadManager
    {
        return $this->bootloader;
    }
}