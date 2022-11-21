<?php

namespace StarLine\Entity;

class Device
{
    readonly int $device_id;
    /**
     * @var string название, присвоенное пользователем
     */
    readonly string $alias;
    /**
     * @var string серийный номер
     */
    readonly string $sn;
    /**
     * @var int время последней активности устройства
     */
    readonly int $activity_ts;
    /**
     * @var string телефонный номер устройства
     */
    readonly string $telephone;
    /**
     * @var int код типа устройства (1-20)
     */
    readonly int $type;
    /**
     * @var string название типа устройства
     */
    readonly string $typename;
    readonly int $reg_date;
    /**
     * @var string версия ПО устройства
     */
    readonly string $firmware_version;
    /**
     * @var string тип батареи (volt)
     */
    readonly string $battery_type;
    /**
     * @var int статус соединения с сервером (1 – online, 2 – offline)
     */
    readonly int $status;
    readonly string $ua_url;

    readonly Position $position;

    /**
     * @var Balance[]
     */
    readonly array $balance;
    /**
     * @var string[] функции устройства
     */
    readonly array $functions;

    readonly array $reed_list;

    readonly Event $event;
    readonly Common $common;
    readonly OBD $obd;
    readonly SysExtraState $sys_extra_state;
    readonly RemoteStart $r_start;
    readonly State $state;
    readonly AlarmState $alarm_state;
}