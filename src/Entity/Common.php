<?php

namespace StarLine\Entity;

class Common
{
    /**
     * @var int температура салона
     */
    readonly int $ctemp;
    /**
     * @var int температура двигателя
     */
    readonly int $etemp;
    /**
     * @var ?int температура маяка
     */
    readonly ?int $mayak_temp;
    /**
     * @var int уровень приёма GSM сигнала (1-30)
     */
    readonly int $gsm_lvl;
    /**
     * @var ?int уровень приёма GPS сигнала, соответствует числу спутников GPS
     */
    readonly ?int $gps_lvl;
    /**
     * @var float напряжение АКБ охранно-телематического комплекса (вольты) или заряд батареи маяка (в процентах)
     */
    readonly float $battery;
    /**
     * @var string тип АКБ
     */
    readonly string $battery_type;
    readonly int $reg_date;
    readonly int $ts;
}