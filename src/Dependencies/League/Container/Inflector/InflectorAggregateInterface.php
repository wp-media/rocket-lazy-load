<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container\Inflector;

use RocketLazyLoadPlugin\Dependencies\League\Container\ImmutableContainerAwareInterface;

interface InflectorAggregateInterface extends ImmutableContainerAwareInterface
{
    /**
     * Add an inflector to the aggregate.
     *
     * @param  string   $type
     * @param  callable $callback
     * @return \RocketLazyLoadPlugin\Dependencies\League\Container\Inflector\Inflector
     */
    public function add($type, callable $callback = null);

    /**
     * Applies all inflectors to an object.
     *
     * @param  object $object
     * @return object
     */
    public function inflect($object);
}
