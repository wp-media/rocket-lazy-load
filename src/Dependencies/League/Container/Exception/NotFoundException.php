<?php

namespace RocketLazyLoadPlugin\Dependencies\League\Container\Exception;

use RocketLazyLoadPlugin\Dependencies\Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
