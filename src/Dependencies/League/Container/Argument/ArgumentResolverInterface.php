<?php

declare(strict_types=1);

namespace RocketLazyLoadPlugin\Dependencies\League\Container\Argument;

use RocketLazyLoadPlugin\Dependencies\League\Container\ContainerAwareInterface;
use ReflectionFunctionAbstract;

interface ArgumentResolverInterface extends ContainerAwareInterface
{
    public function resolveArguments(array $arguments): array;
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []): array;
}
