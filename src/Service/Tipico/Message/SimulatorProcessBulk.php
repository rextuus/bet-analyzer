<?php

namespace App\Service\Tipico\Message;

enum SimulatorProcessBulk : string
{
    case THREE_WAY_SIMULATORS = 'three_way_simulators';
    case OVER_UNDER_SIMULATORS = 'over_under_simulators';
}
