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
    case H2H_HOME = "head_to_head_home";
    case H2H_AWAY = "head_to_head_away";
}
