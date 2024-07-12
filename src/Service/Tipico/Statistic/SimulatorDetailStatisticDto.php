<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Statistic\PlacementDistribution\WeekDayPlacementDistribution;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorDetailStatisticDto
{
    /**
     * @var array<WeekDayPlacementDistribution>
     */
    private array $weekDayPlacementDistributions;

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
}
