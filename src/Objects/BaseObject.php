<?php

namespace StarLine\Objects;

use Exception;

class BaseObject
{
    protected array $extra;

    /**
     * @var string[] обязательные поля объекта
     */
    protected array $required = [];

    /**
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->extra = $data;
    }

    function __get(string $name)
    {
        return $this->extra[$name] ?? null;
    }

    public function isValid(): bool
    {
        foreach ($this->required as $name) {
            if (!isset($this->extra[$name])) {
                return false;
            }
        }
        return true;
    }
}