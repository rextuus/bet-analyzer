<?php
declare(strict_types=1);

namespace App\Service\Evaluation;


class SlideWindowFactory
{
    public function calculateStepsForSlideWindow(float $min, float $max, float $interval): array
    {
        $steps = [];
        $to = $min + $interval;
        for ($from = $min; $from <= $max - $interval; $from = $from + $interval){
            $steps[] = ['from' => $from, 'to' => $to];
            $to = $to + $interval;
        }
        return $steps;
    }

    public function calculateStepsForDecreasingWindow(float $min, float $max, float $interval): array
    {
        $steps = [];
        $to = $max;
        for ($from = $min; $from < $max; $from = $from + $interval){
            $steps[] = ['from' => $from, 'to' => $to];
        }
        return $steps;
    }

    public function convertWindowToMap(array $steps): array
    {
        $map = [];
        foreach ($steps as $step){
            $map[implode('-', $step)] = false;
        }
        return $map;
    }
}
