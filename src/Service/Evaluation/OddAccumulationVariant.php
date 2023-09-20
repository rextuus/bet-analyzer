<?php

namespace App\Service\Evaluation;

enum OddAccumulationVariant: string
{
    case MEDIAN = "median";
    case MIN = "min";
    case MAX = "max";
    case AVG = "avg";
}
