<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Entity\BettingProvider\TipicoPlacement;

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
        return array_map(
            function (array $placements) {
                return self::calculateSumForPlacements($placements);
            },
            $distribution
        );
    }
}
