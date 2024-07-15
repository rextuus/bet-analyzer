<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Statistic\PlacementDistribution\WeekDayPlacementDistribution;

class SimulatorDetailStatisticDto
{
    /**
     * @var array<WeekDayPlacementDistribution>
     */
    private array $weekDayPlacementDistributions;

    private int $longestNonWinningPeriod;

    private float $highestLost;

    public function getWeekDayPlacementDistributionByWeekday(WeekDay $weekDay): ?WeekDayPlacementDistribution
    {
        foreach ($this->weekDayPlacementDistributions as $weekDayPlacementDistribution) {
            if ($weekDayPlacementDistribution->getWeekDay() === $weekDay) {
                return $weekDayPlacementDistribution;
            }
        }

        return null;
    }

    public function getWeekDayPlacementDistributions(): array
    {
        return $this->weekDayPlacementDistributions;
    }

    public function setWeekDayPlacementDistributions(array $weekDayPlacementDistributions): SimulatorDetailStatisticDto
    {
        $this->weekDayPlacementDistributions = $weekDayPlacementDistributions;
        return $this;
    }

    public function getLongestNonWinningPeriod(): int
    {
        return $this->longestNonWinningPeriod;
    }

    public function setLongestNonWinningPeriod(int $longestNonWinningPeriod): SimulatorDetailStatisticDto
    {
        $this->longestNonWinningPeriod = $longestNonWinningPeriod;
        return $this;
    }

    public function getHighestLost(): float
    {
        return $this->highestLost;
    }

    public function setHighestLost(float $highestLost): SimulatorDetailStatisticDto
    {
        $this->highestLost = $highestLost;
        return $this;
    }
}
