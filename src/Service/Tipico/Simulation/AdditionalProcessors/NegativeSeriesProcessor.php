<?php
declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class NegativeSeriesProcessor
{
    /**
     * @param TipicoPlacementData[] $placementData
     */
    public function processNegativeSeriesBreakParameter(
        array $placementData,
        int $negativeSeriesBreak,
        int $currentNegativeSeriesCount
    ): AdditionalProcessResult
    {
        $result = new AdditionalProcessResult();

        $waitForNextWinningFixture = false;
        if ($currentNegativeSeriesCount >= $negativeSeriesBreak) {
            $waitForNextWinningFixture = true;
        }

        $actuallyDonePlacements = [];
        foreach ($placementData as $placement) {
            // we wait for the end of a loosing series
            if ($waitForNextWinningFixture) {
                // No loose yeah => try the next bet
                if ($placement->isWon()) {
                    $currentNegativeSeriesCount = 0;
                    $waitForNextWinningFixture = false;
                    continue;
                }
                // loose again => just increase the series
                $currentNegativeSeriesCount++;
                continue;
            }

            // its currently no loosing series => lets place bets
            $actuallyDonePlacements[] = $placement;
            if ($placement->isWon()) {
                $currentNegativeSeriesCount = 0;
            } else {
                $currentNegativeSeriesCount++;
            }

            if ($currentNegativeSeriesCount === $negativeSeriesBreak) {
                $waitForNextWinningFixture = true;
            }
        }

        $result->setPlacementData($actuallyDonePlacements);
        $result->setCurrentNegativeSeries($currentNegativeSeriesCount);

        return $result;
    }
}
