<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

use RocketLazyLoadPlugin\Dependencies\Interop\Container\ContainerInterface as InteropContainerInterface;

interface ImmutableContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \RocketLazyLoadPlugin\Dependencies\Interop\Container\ContainerInterface $container
     */
    public function setContainer(InteropContainerInterface $container);

    /**
     * Get the container
     *
     * @return \RocketLazyLoadPlugin\Dependencies\League\Container\ImmutableContainerInterface
     */
    public function getContainer();
}
