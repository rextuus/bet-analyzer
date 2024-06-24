<?php

declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;

class WeekdaySpecificProcessor implements AdditionalProcessorInterface
{
    public function getIdentifier(): string
    {
        return AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS;
    }

    public function process(AdditionalProcessResult $result, array $parameters): AdditionalProcessResult
    {
        $placementData = $result->getPlacementData();
        $result = new AdditionalProcessResult();

        $weekdays = [];
        foreach ($parameters[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS] as $weekday) {
            $weekdays[] = Weekday::from($weekday);
        }

        $actuallyDonePlacements = [];
        foreach ($placementData as $placement) {
            foreach ($weekdays as $weekday) {
                $placementDate = $placement->getCreated()->format('N');
                if ((int)$placementDate === $weekday->value) {
                    $actuallyDonePlacements[] = $placement;
                }
            }
        }

        $result->setPlacementData($actuallyDonePlacements);


        return $result;
    }
}