<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container\Exception;

use RocketLazyLoadPlugin\Dependencies\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
