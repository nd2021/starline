<?php

namespace StarLine\Entity;

class AlarmState
{
    readonly bool $door;
    readonly bool $shock_h;
    readonly bool $hood;
    readonly bool $hbrake;
    readonly bool $shock_l;
    readonly bool $add_h;
    readonly bool $run;
    readonly bool $add_l;
    readonly bool $hijack;
    readonly bool $tilt;
    readonly bool $trunk;
    readonly bool $pbrake;
    readonly int $ts;
}