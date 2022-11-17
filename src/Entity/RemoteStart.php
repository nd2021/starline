<?php

namespace StarLine\Entity;

class RemoteStart
{
    readonly int $wakeup_ts;
    readonly array $temp;
    readonly array $battery;
    readonly array $period;
    readonly array $cron;
}