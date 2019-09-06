<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

use RocketLazyLoadPlugin\Dependencies\Interop\Container\ContainerInterface as InteropContainerInterface;

trait ImmutableContainerAwareTrait
{
    /**
     * @var \RocketLazyLoadPlugin\Dependencies\Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * Set a container.
     *
     * @param  \RocketLazyLoadPlugin\Dependencies\Interop\Container\ContainerInterface $container
     * @return $this
     */
    public function setContainer(InteropContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the container.
     *
     * @return \RocketLazyLoadPlugin\Dependencies\League\Container\ImmutableContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
