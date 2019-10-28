<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Core\Container;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Exception\Container\AutowireException;
use Spiral\Core\FactoryInterface;

/**
 * Provides ability to delegate option to container.
 */
final class Autowire
{
    /** @var mixed */
    private $alias;

    /** @var array */
    private $parameters = [];

    /**
     * Autowire constructor.
     *
     * @param string $alias
     * @param array  $parameters
     */
    public function __construct(string $alias, array $parameters = [])
    {
        $this->alias = $alias;
        $this->parameters = $parameters;
    }

    /**
     * @param $an_array
     * @return static
     */
    public static function __set_state($an_array)
    {
        return new static($an_array['alias'], $an_array['parameters']);
    }

    /**
     * @param FactoryInterface $factory
     * @param array            $parameters Context specific parameters (always prior to declared ones).
     * @return mixed
     *
     * @throws AutowireException  No entry was found for this identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function resolve(FactoryInterface $factory, array $parameters = [])
    {
        return $factory->make($this->alias, $parameters + $this->parameters);
    }
}
