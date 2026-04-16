<?php

declare(strict_types=1);

namespace RocketLazyLoadPlugin\Dependencies\League\Container\Argument\Literal;

use RocketLazyLoadPlugin\Dependencies\League\Container\Argument\LiteralArgument;

class ObjectArgument extends LiteralArgument
{
    public function __construct(object $value)
    {
        parent::__construct($value, LiteralArgument::TYPE_OBJECT);
    }
}
