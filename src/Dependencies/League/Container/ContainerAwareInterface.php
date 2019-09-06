<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

interface ContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \RocketLazyLoadPlugin\Dependencies\League\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get the container
     *
     * @return \RocketLazyLoadPlugin\Dependencies\League\Container\ContainerInterface
     */
    public function getContainer();
}
