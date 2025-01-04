<?php

namespace App\Service\Evaluation;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
enum OddAccumulationVariant: string
{
    case MEDIAN = "median";
    case MIN = "min";
    case MAX = "max";
    case AVG = "avg";
}
