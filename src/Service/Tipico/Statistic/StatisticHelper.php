<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Entity\BettingProvider\TipicoPlacement;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use DateInterval;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class StatisticHelper
{
    /**
     * @param array<TipicoPlacement> $placements
     * @return array<string, array<TipicoPlacement>>
     */
    public static function getDailyPlacementDistribution(array $placements): array
    {
        $orderedPlacements = [];
        foreach ($placements as $placement) {
            $key = $placement->getCreated()->format('d-m-Y');
            if (!array_key_exists($key, $orderedPlacements)) {
                $orderedPlacements[$key] = [];
            }
            $orderedPlacements[$key][] = $placement;
        }

        return $orderedPlacements;
    }

    /**
     * @param array<TipicoPlacement> $placements
     * @return array<string, array<TipicoPlacement>>
     */
    public static function getWeekDailPlacementDistribution(array $placements): array
    {
        $orderedPlacements = [];
        foreach ($placements as $placement) {
            $key = $placement->getCreated()->format('N');
            if (!array_key_exists($key, $orderedPlacements)) {
                $orderedPlacements[$key] = [];
            }
            $orderedPlacements[$key][] = $placement;
        }

        return $orderedPlacements;
    }

    /**
     * @param array<TipicoPlacement> $placements
     * @return array<string, array<float>>
     */
    public static function getWeekdayPlacementDistributionWithCalculatedCashBoxes(array $placements): array
    {
        $distribution = self::getWeekDailPlacementDistribution($placements);

//        $sums = array_map(
//            function (array $placements) {
//                return self::calculateSumForPlacements($placements);
//            },
//            $distribution
//        );
//        ksort($sums);
        ksort($distribution);

        $result = [];
        foreach ($distribution as $key => $placements) {
            $result[Weekday::tryFrom((int)$key)->name] = round(
                self::calculateSumForPlacements($placements) / count($placements),
                2
            );
        }


        // fill empty days

//        dd($result);
//        $firstDay = array_key_first($distribution);
//        $dates = self::getDates($firstDay);
//
//        $ordered = [];
//        foreach ($dates as $date) {
//            $ordered[$date] = $distribution[$date] ?? 0.0;
//        }

        return $result;
    }

    /**
     * @param array<TipicoPlacement> $placements
     * @return float
     */
    public static function calculateSumForPlacements(array $placements): float
    {
        return array_sum(
            array_map(
                function (TipicoPlacement $placement) {
                    return $placement->getCalculatedValue();
                },
                $placements
            )
        );
    }

    /**
     * @param array<TipicoPlacement> $placements
     * @return array<string, array<float>>
     */
    public static function getDailyPlacementDistributionWithCalculatedCashBoxes(array $placements): array
    {
        $distribution = self::getDailyPlacementDistribution($placements);
        $distribution = array_map(
            function (array $placements) {
                return self::calculateSumForPlacements($placements);
            },
            $distribution
        );

        // fill empty days
        $firstDay = array_key_first($distribution);
        $dates = self::getDates($firstDay);

        $ordered = [];
        foreach ($dates as $date) {
            $ordered[$date] = $distribution[$date] ?? 0.0;
        }

        return $ordered;
    }

    private static function getDates($startDate)
    {
        $dates = [];
        $start = DateTime::createFromFormat('d-m-Y', $startDate);
        $now = new DateTime('now');
        $interval = new DateInterval('P1D');

        $weekday = $start->format('N');

        for ($date = clone $start; $date <= $now; $date->add($interval)) {
            if ($date->format('N') === $weekday) {
                $dates[] = $date->format('d-m-Y');
            }
        }

        return $dates;
    }
}
