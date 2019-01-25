<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Bootloader\BootloaderInterface;
use Spiral\Core\Exception\BootloadException;

/**
 * Provides ability to bootload ServiceProviders.
 */
class BootloadManager
{
    /**
     * List of bootloaded classes.
     *
     * @var array
     */
    private $classes = [];

    /**
     * @invisible
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get bootloaded classes.
     *
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Bootload set of classes
     *
     * @param array $classes
     *
     * @throws BootloadException
     */
    public function bootload(array $classes)
    {
        try {
            $this->boot($classes);
        } catch (\Throwable|ContainerExceptionInterface $e) {
            throw new BootloadException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Generate cached bindings schema.
     *
     * @param array $classes
     *
     * @throws ContainerExceptionInterface
     * @throws \Error
     */
    protected function boot(array $classes)
    {
        foreach ($classes as $class) {
            $this->classes[] = $class;
            $bootloader = $this->container->get($class);

            if (!$bootloader instanceof BootloaderInterface) {
                continue;
            }

            $reflection = new \ReflectionClass($bootloader);

            $this->initBindings($bootloader->defineBindings(), $bootloader->defineSingletons());

            //Can be booted based on it's configuration
            if ((bool)$reflection->getConstant('BOOT')) {
                $boot = new \ReflectionMethod($bootloader, 'boot');
                $boot->invokeArgs($bootloader, $this->container->resolveArguments($boot));
            }
        }
    }

    /**
     * Bind declared bindings.
     *
     * @param array $bindings
     * @param array $singletons
     */
    protected function initBindings(array $bindings, array $singletons)
    {
        foreach ($bindings as $aliases => $resolver) {
            $this->container->bind($aliases, $resolver);
        }

        foreach ($singletons as $aliases => $resolver) {
            $this->container->bindSingleton($aliases, $resolver);
        }
    }
}