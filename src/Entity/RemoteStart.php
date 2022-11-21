<?php

namespace StarLine\Entity;

/**
 * Параметры автозапуска
 */
class RemoteStart
{
    readonly int $wakeup_ts;
    /**
     * @var array по температуре двигателя
     */
    readonly array $temp;
    /**
     * @var array по напряжению АКБ
     */
    readonly array $battery;
    /**
     * @var array периодический запуск
     */
    readonly array $period;
    readonly array $cron;

    /**
     * Установлены ли настройки автозапуска
     *
     * @param string|null $type (temp, battery, period, cron, null - любой из имеющихся)
     * @return bool
     */
    public function has(string $type = null): bool
    {
        $types = ['temp', 'battery', 'period', 'cron'];
        if (is_null($type)) {
            foreach ($types as $type) {
                if (!(isset(($this->$type)['has']) && ($this->$type)['has'] === false)) {
                    return true;
                }
            }
        } elseif(in_array($type, $types)) {
            return !(isset(($this->$type)['has']) && ($this->$type)['has'] === false);
        }
        return false;
    }
}