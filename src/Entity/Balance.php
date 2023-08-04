<?php

namespace StarLine\Entity;

class Balance
{
    /**
     * @var int баланс
     */
    readonly int $value;
    /**
     * @var string ссылка на оплату
     */
    readonly string $url_payment;
    /**
     * @var int код состояния баланса
     */
    readonly int $state;
    /**
     * @var string валюта
     */
    readonly string $currency;
    /**
     * @var string тег счета (active)
     */
    readonly string $key;
    /**
     * @var string оператор
     */
    readonly string $operator;
    readonly int $ts;

    readonly string $number;

    readonly int $slot;
}