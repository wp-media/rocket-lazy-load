<?php

declare(strict_types=1);

namespace RocketLazyLoadPlugin\Dependencies\League\Container;

interface ContainerAwareInterface
{
    public function getContainer(): DefinitionContainerInterface;
    public function setContainer(DefinitionContainerInterface $container): ContainerAwareInterface;
}
