<?php

namespace StarLine\Entity;

class Balance
{
    /**
     * @var int баланс
     */
    readonly int $value;
    readonly string $url_payment;
    /**
     * @var int код состояния баланса
     */
    readonly int $state;
    readonly string $currency;
    /**
     * @var string тег счета (active)
     */
    readonly string $key;
    readonly string $operator;
    readonly int $ts;
}