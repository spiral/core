<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Bootloader\BootloaderInterface;
use Spiral\Core\Bootloader\DependedInterface;
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
     * Bootload set of classes. Support short and extended syntax with
     * bootload options (to be passed into boot method).
     *
     * [
     *    SimpleBootloader::class,
     *    CustomizedBootloader::class => ["option" => "value"]
     * ]
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
     * Bootloader all given classes.
     *
     * @param array $classes
     *
     * @throws ContainerExceptionInterface
     * @throws \ReflectionException
     */
    protected function boot(array $classes)
    {
        foreach ($classes as $class => $options) {
            // default bootload syntax as simple array
            if (is_string($options)) {
                $class = $options;
                $options = [];
            }

            if (in_array($class, $this->classes)) {
                continue;
            }

            $this->classes[] = $class;
            $bootloader = $this->container->get($class);

            if (!$bootloader instanceof BootloaderInterface) {
                continue;
            }

            $this->initBootloader($bootloader, $options);
        }
    }

    /**
     * @param BootloaderInterface $bootloader
     * @param array               $options
     *
     * @throws \ReflectionException
     */
    protected function initBootloader(BootloaderInterface $bootloader, array $options = [])
    {
        if ($bootloader instanceof DependedInterface) {
            $this->boot($bootloader->defineDependencies());
        }

        $this->initBindings($bootloader->defineBindings(), $bootloader->defineSingletons());

        if ((new \ReflectionClass($bootloader))->hasMethod('boot')) {
            $boot = new \ReflectionMethod($bootloader, 'boot');
            $boot->invokeArgs($bootloader, $this->container->resolveArguments($boot, $options));
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