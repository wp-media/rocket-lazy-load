<?php

declare(strict_types=1);

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

use RocketLazyLoadPlugin\Dependencies\League\Container\Definition\DefinitionInterface;
use RocketLazyLoadPlugin\Dependencies\League\Container\Inflector\InflectorInterface;
use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\ServiceProviderInterface;
use RocketLazyLoadPlugin\Dependencies\Psr\Container\ContainerInterface;

interface DefinitionContainerInterface extends ContainerInterface
{
    public function add(string $id, $concrete = null): DefinitionInterface;
    public function addServiceProvider(ServiceProviderInterface $provider): self;
    public function addShared(string $id, $concrete = null): DefinitionInterface;
    public function extend(string $id): DefinitionInterface;
    public function getNew($id);
    public function inflector(string $type, ?callable $callback = null): InflectorInterface;
}
