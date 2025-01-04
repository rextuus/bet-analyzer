<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Statistic\PlacementDistribution\WeekDayPlacementDistribution;
use Symfony\UX\Chartjs\Model\Chart;

class SimulatorDetailStatisticDto
{
    /**
     * @var array<WeekDayPlacementDistribution>
     */
    private array $weekDayPlacementDistributions;

    private ?NegativePeriod $longestNonWinningPeriod;

    private ?NegativePeriod $highestLost;

    private Chart $weekDayChart;

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

    public function getLongestNonWinningPeriod(): ?NegativePeriod
    {
        return $this->longestNonWinningPeriod;
    }

    public function setLongestNonWinningPeriod(?NegativePeriod $longestNonWinningPeriod): SimulatorDetailStatisticDto
    {
        $this->longestNonWinningPeriod = $longestNonWinningPeriod;
        return $this;
    }

    public function getHighestLost(): ?NegativePeriod
    {
        return $this->highestLost;
    }

    public function setHighestLost(?NegativePeriod $highestLost): SimulatorDetailStatisticDto
    {
        $this->highestLost = $highestLost;
        return $this;
    }

    public function getWeekDayChart(): Chart
    {
        return $this->weekDayChart;
    }

    public function setWeekDayChart(Chart $weekDayChart): SimulatorDetailStatisticDto
    {
        $this->weekDayChart = $weekDayChart;
        return $this;
    }
}
