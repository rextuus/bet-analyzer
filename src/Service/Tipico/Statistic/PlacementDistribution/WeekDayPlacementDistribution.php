<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic\PlacementDistribution;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Statistic\StatisticHelper;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class WeekDayPlacementDistribution extends BasePlacementDistribution
{
    private Weekday $weekDay;

    private Chart $chart;

    private string $isActiveClass = '';

    public function getWeekDay(): Weekday
    {
        return $this->weekDay;
    }

    public function setWeekDay(Weekday $weekDay): WeekDayPlacementDistribution
    {
        $this->weekDay = $weekDay;
        return $this;
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setChart(Chart $chart): WeekDayPlacementDistribution
    {
        $this->chart = $chart;
        return $this;
    }

    public function getTotalSum(): float
    {
        return StatisticHelper::calculateSumForPlacements($this->getPlacements());
    }

    public function getIsActiveClass(): string
    {
        $currentDate = new \DateTime();
        $weekDayValue = $currentDate->format('N');

        if ($this->getWeekDay() === Weekday::tryFrom((int)$weekDayValue)) {
            if ($this->getTotalSum() >= 0.0) {
                return 'currentDay positive';
            }
            return 'currentDay negative';
        }


        return $this->isActiveClass;
    }

    public function setIsActiveClass(string $isActiveClass): WeekDayPlacementDistribution
    {
        $this->isActiveClass = $isActiveClass;
        return $this;
    }
}
