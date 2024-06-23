<?php

namespace App\Service\Tipico\Content\SimulationStrategy;

enum AdditionalProcessingIdent: string
{
    case EMPTY = "";
    case STOP_NEGATIVE_SERIES = "stop_negative_series";
    case USER_RANDOM_INPUT = "user_random_input";
}
