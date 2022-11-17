<?php

namespace StarLine\Entity;

class OBD
{
    /**
     * @var float количество топлива в литрах
     */
    readonly float $fuel_litres;
    /**
     * @var float количество топлива в литрах
     */
    readonly float $fuel_percent;
    readonly int $dist_to_empty;
    /**
     * @var int Пробег автомобиля в километрах
     */
    readonly int $mileage;
    readonly int $ts;
}