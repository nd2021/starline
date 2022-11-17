<?php

namespace StarLine\Attribute;

use Attribute;

#[Attribute]
class Field
{
    /**
     * @param string|null $from_name
     * @param string|null $array_of
     */
    function __construct(
        public ?string $from_name = null,
        public ?string $array_of = null
    ) {
    }
}