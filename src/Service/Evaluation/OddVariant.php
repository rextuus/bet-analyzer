<?php

namespace App\Service\Evaluation;

enum OddVariant: string
{
    case CLASSIC_3_WAY = "Fulltime Result";
    case BOTH_TEAMS_SCORE = "both_teams_score";
    case HEAD_TO_HEAD = "head_to_head";
    case OVER_UNDER = "over_under";
}
