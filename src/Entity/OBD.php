<?php

namespace StarLine\Entity;

class OBD
{
    /**
     * @var ?int количество топлива в литрах
     */
    readonly ?int $fuel_litres;
    /**
     * @var ?int количество топлива в литрах
     */
    readonly ?int $fuel_percent;
    readonly ?int $dist_to_empty;
    /**
     * @var int Пробег автомобиля в километрах
     */
    readonly int $mileage;
    readonly int $ts;
}