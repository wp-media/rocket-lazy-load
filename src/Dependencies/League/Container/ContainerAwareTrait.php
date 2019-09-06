<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

trait ContainerAwareTrait
{
    /**
     * @var \RocketLazyLoadPlugin\Dependencies\League\Container\ContainerInterface
     */
    protected $container;

    /**
     * Set a container.
     *
     * @param  \RocketLazyLoadPlugin\Dependencies\League\Container\ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the container.
     *
     * @return \RocketLazyLoadPlugin\Dependencies\League\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
