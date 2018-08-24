<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerInterface;

/**
 * Auto-wiring container: declarative singletons, contextual injections, parent container delegation and
 * ability to lazy wire.
 *
 * Container does not support setter injections, private properties and etc. Normally it will work
 * with classes only to be as much invisible as possible. Attention, this is hungry implementation
 * of container, meaning it WILL try to resolve dependency unless you specified custom lazy factory.
 *
 * You can use injectors to delegate class resolution to external container.
 *
 * @see \Spiral\Core\Container::registerInstance() to add your own behaviours.
 *
 * @see InjectableInterface
 * @see SingletonInterface
 */
class Container implements ContainerInterface
{
    /**
     * Parent container responsible for low level dependency configuration (i.e. config based).
     *
     * @var ContainerInterface
     */
    private $parent = null;
}