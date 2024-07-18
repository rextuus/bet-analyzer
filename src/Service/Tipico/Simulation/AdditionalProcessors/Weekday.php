<?php

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Entity\BettingProvider\Simulator;

enum Weekday: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public static function getSimulatorWeekDayTotalValue(Simulator $simulator, Weekday $day): string
    {
        $statistic = $simulator->getSimulatorDetailStatistic();
        if ($statistic === null) {
            return '-';
        }

        $value = 0.0;
        match ($day) {
            Weekday::Monday => $value = $statistic->getMondayTotal(),
            Weekday::Tuesday => $value = $statistic->getTuesdayTotal(),
            Weekday::Wednesday => $value = $statistic->getWednesdayTotal(),
            Weekday::Thursday => $value = $statistic->getThursdayTotal(),
            Weekday::Friday => $value = $statistic->getFridayTotal(),
            Weekday::Saturday => $value = $statistic->getSaturdayTotal(),
            Weekday::Sunday => $value = $statistic->getSundayTotal(),
        };

        return (string)round($value, 2);
    }

    public static function getSimulatorWeekDayAverageValue(Simulator $simulator, Weekday $day): string
    {
        $statistic = $simulator->getSimulatorDetailStatistic();
        if ($statistic === null) {
            return '-';
        }

        $value = 0.0;
        match ($day) {
            Weekday::Monday => $value = $statistic->getMondayAverage(),
            Weekday::Tuesday => $value = $statistic->getTuesdayAverage(),
            Weekday::Wednesday => $value = $statistic->getWednesdayAverage(),
            Weekday::Thursday => $value = $statistic->getThursdayAverage(),
            Weekday::Friday => $value = $statistic->getFridayAverage(),
            Weekday::Saturday => $value = $statistic->getSaturdayAverage(),
            Weekday::Sunday => $value = $statistic->getSundayAverage(),
        };

        return (string)round($value, 2);
    }
}
