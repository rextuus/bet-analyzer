<?php

namespace App\Service\Evaluation;

enum BetOn: string
{
    case HOME = "1";
    case DRAW = "X";
    case AWAY = "2";
    case OVER = "over";
    case UNDER = "under";
}
