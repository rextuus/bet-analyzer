<?php
declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;


class NegativeSeriesProcessor implements AdditionalProcessorInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function process(
        AdditionalProcessResult $result,
        array $parameters
    ): AdditionalProcessResult
    {
        $placementData = $result->getPlacementData();

        $result = new AdditionalProcessResult();

        $negativeSeriesBreak = (int)$parameters[AbstractSimulationProcessor::PARAMETER_NEGATIVE_SERIES_BREAK_POINT];
        $currentNegativeSeriesCount = (int)$parameters[AbstractSimulationProcessor::PARAMETER_CURRENT_NEGATIVE_SERIES];

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

        $result->setProcessedNegativeSeries(true);

        return $result;
    }

    public function getIdentifier(): string
    {
        return AbstractSimulationProcessor::PARAMETER_NEGATIVE_SERIES_BREAK_POINT;
    }
}
