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
    readonly string $battery_type;
    /**
     * @var int статус соединения с сервером (1 – online, 2 – offline)
     */
    readonly int $status;
    readonly string $ua_url;

    protected Position $position;

    /**
     * @var Balance[]
     */
    protected array $balance = [];
    /**
     * @var string[] функции устройства
     */
    protected array $functions = [];
    protected array $reed_list = [];

    protected Event $event;
    protected Common $common;
    protected OBD $obd;
    protected SysExtraState $tag_low_voltage;
    protected RemoteStart $r_start;

    public $json = '{
                "alarm_state": {
                    "door": false,
                    "shock_h": false,
                    "hood": false,
                    "ts": 1667848838,
                    "hbrake": false,
                    "shock_l": false,
                    "add_h": false,
                    "run": false,
                    "add_l": false,
                    "hijack": false,
                    "tilt": false,
                    "trunk": false,
                    "pbrake": false
                }
            }';

}