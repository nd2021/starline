<?php

namespace StarLine\Entity;

class Position
{
    /**
     * @var string широта
     */
    readonly string $x;
    /**
     * @var string долгота
     */
    readonly string $y;
    /**
     * @var int точность в метрах (при определении местоположения объекта по базовым станциям GSM)
     */
    readonly int $r;
    /**
     * @var int скорость в км/ч
     */
    readonly int $s;
    readonly bool $is_move;
    /**
     * @var int направление движения в градусах от 0 до 360 (0 – Север, 180 – Юг)
     */
    readonly int $dir;
    /**
     * @var int число принимаемых спутников GPS
     */
    readonly int $sat_qty;
    readonly int $ts;
}