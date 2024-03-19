<?php

namespace App\Service\Evaluation;

enum BetOn: string
{
    case HOME = "1";
    case DRAW = "X";
    case AWAY = "2";
    case OVER = "over";
    case UNDER = "under";
    case BOTH_TEAMS_SCORE = "both_teams_score";
    case BOTH_TEAMS_SCORE_NOT = "both_teams_score_not";
}
