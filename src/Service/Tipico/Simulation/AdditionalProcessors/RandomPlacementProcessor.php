<?php

declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class RandomPlacementProcessor implements AdditionalProcessorInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function process(
        AdditionalProcessResult $result,
        array $parameters,
    ): AdditionalProcessResult {
        $placementData = $result->getPlacementData();

        $inputVariants = $parameters[AbstractSimulationProcessor::PARAMETER_USE_RANDOM_INPUT];

        foreach ($placementData as $placement) {
            $input = (float)$inputVariants[array_rand($inputVariants)];
            $placement->setInput($input);
        }

        $result->setPlacementData($placementData);

        $result->setProcessesRandomInput(true);

        return $result;
    }

    public function getIdentifier(): string
    {
        return AbstractSimulationProcessor::PARAMETER_USE_RANDOM_INPUT;
    }
}
