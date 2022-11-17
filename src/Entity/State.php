<?php

namespace StarLine\Entity;

class State
{
    /**
     * @var bool (*) состояние дверей
     */
    readonly bool $door;
    /**
     * @var bool (*) состояние капота
     */
    readonly bool $hood;
    /**
     * @var bool (*) состояние багажника
     */
    readonly bool $trunk;
    /**
     * @var bool (*) состояние двигателя
     */
    readonly bool $ign;
    /**
     * @var bool (*) состояние ручного тормоза
     */
    readonly bool $hbrake;
    /**
     * @var bool состояние режима "Свободные руки"
     */
    readonly bool $hfree;
    /**
     * @var bool (*) состояние режима "Антиограбление"
     */
    readonly bool $hijack;
    /**
     * @var bool (*) состояние датчика удара
     */
    readonly bool $shock_bpass;
    /**
     * @var bool (*) состояние датчика наклона
     */
    readonly bool $tilt_bpass;
    /**
     * @var bool (*) статус тревоги сигнализации
     */
    readonly bool $alarm;
    /**
     * @var bool (*) состояние режима охраны
     */
    readonly bool $arm;
    /**
     * @var bool режим подтверждения авторизации (для устройств 6-го поколения)
     */
    readonly bool $arm_auth_wait;
    /**
     * @var bool режим запрета поездки (для устройств 6-го поколения)
     */
    readonly bool $arm_moving_pb;
    readonly bool $superuser;
    /**
     * @var bool (*) статус сервисного режима
     */
    readonly bool $valet;
    /**
     * @var bool (*) состояние дополнительного канала
     */
    readonly bool $out;
    /**
     * @var bool режим программной нейтрали
     */
    readonly bool $neutral;
    readonly int $dvr_timer;
    readonly int $webasto_timer;
    /**
     * @var bool (*) статус дистанционного запуска
     */
    readonly bool $r_start;
    readonly int $r_start_timer;
    /**
     * @var bool состояние предпускового подогревателя
     */
    readonly bool $webasto;

    /**
     * @var bool (*) состояние дополнительного датчика
     */
    readonly bool $add_sens_bpass;
    readonly bool $dvr;
    /**
     * @var bool (*) состояние зажигания
     */
    readonly bool $run;
    /**
     * @var bool состояние педали тормоза
     */
    readonly bool $pbrake;

    public $motohrs;
    public $hlock;
    readonly int $ts;
}