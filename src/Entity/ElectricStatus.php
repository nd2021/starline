<?php

namespace StarLine\Entity;

class ElectricStatus
{
    readonly ?bool $charging;
    readonly ?bool $battery_percents;
    readonly ?bool $battery_wt;
}