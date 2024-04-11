<?php

namespace App\Service\Tipico\Message;

enum SimulatorProcessBulk : string
{
    case THREE_WAY_SIMULATORS = 'three_way_simulators';
    case OVER_UNDER_SIMULATORS = 'over_under_simulators';
    case BOTH_TEAMS_SCORE_SIMULATORS = 'both_teams_score_simulators';
    case HEAD_TO_HEAD_SIMULATORS = 'head_to_head_simulators';
    case THREE_WAY_SIMULATORS_NSB = 'three_way_simulators_nsb';
    case BOTH_TEAMS_SCORE_SIMULATORS_NSB = 'both_teams_score_simulators_nsb';
    case HEAD_TO_HEAD_SIMULATORS_NSB = 'head_to_head_simulators_nsb';
    case OVER_UNDER_SIMULATORS_NSB = 'over_under_simulators_nsb';
}
