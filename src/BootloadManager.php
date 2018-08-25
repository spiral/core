<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Bootloaders\BootloaderInterface;
use Spiral\Core\Exceptions\BootloadException;

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
            $schema = $this->generateSchema($classes, $this->container);
            $this->bootSchema($this->container, $schema);
        } catch (\Throwable|ContainerExceptionInterface $e) {
            throw new BootloadException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Bootload based on schema.
     *
     * @param Container $container
     * @param array     $schema
     *
     * @throws ContainerExceptionInterface
     * @throws \Error
     */
    protected function bootSchema(Container $container, array $schema)
    {
        foreach ($schema['bootloaders'] as $bootloader => $options) {
            $this->classes[] = $bootloader;

            if (array_key_exists('bindings', $options)) {
                $this->initBindings($container, $options);
            }

            if ($options['init']) {
                $object = $container->get($bootloader);

                if ($options['boot']) {
                    //Booting
                    $boot = new \ReflectionMethod($object, 'boot');
                    $boot->invokeArgs($object, $container->resolveArguments($boot));
                }
            }
        }
    }

    /**
     * Generate cached bindings schema.
     *
     * @param array     $classes
     * @param Container $container
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws \Error
     */
    protected function generateSchema(array $classes, Container $container): array
    {
        $schema = [
            'snapshot'    => $classes,
            'bootloaders' => []
        ];

        foreach ($classes as $class) {
            $this->classes[] = $class;

            $initSchema = ['init' => true, 'boot' => false];
            $bootloader = $container->get($class);

            if ($bootloader instanceof BootloaderInterface) {
                $initSchema['bindings'] = $bootloader->defineBindings();
                $initSchema['singletons'] = $bootloader->defineSingletons();

                $reflection = new \ReflectionClass($bootloader);

                //Can be booted based on it's configuration
                $initSchema['boot'] = (bool)$reflection->getConstant('BOOT');
                $initSchema['init'] = $initSchema['boot'];

                //Let's initialize now
                $this->initBindings($container, $initSchema);
            } else {
                $initSchema['init'] = true;
            }

            //Need more checks here
            if ($initSchema['boot']) {
                $boot = new \ReflectionMethod($bootloader, 'boot');
                $boot->invokeArgs($bootloader, $container->resolveArguments($boot));
            }

            $schema['bootloaders'][$class] = $initSchema;
        }

        return $schema;
    }

    /**
     * Bind declared bindings.
     *
     * @param Container $container
     * @param array     $bootSchema
     */
    protected function initBindings(Container $container, array $bootSchema)
    {
        foreach ($bootSchema['bindings'] as $aliases => $resolver) {
            $container->bind($aliases, $resolver);
        }

        foreach ($bootSchema['singletons'] as $aliases => $resolver) {
            $container->bindSingleton($aliases, $resolver);
        }
    }
}