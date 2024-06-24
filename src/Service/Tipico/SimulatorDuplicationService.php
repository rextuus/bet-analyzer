<?php

declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\BettingProvider\Simulator;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorDuplicationService
{
    public function __construct(
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly SimulatorService $simulatorService
    ) {
    }

    public function duplicateSimulatorAndLimitToWeekday(Simulator $simulator, Weekday $weekday): Simulator
    {
        $simulatorData = (new SimulatorData())->initFromEntity($simulator);
        $simulatorStrategyData = (new SimulationStrategyData())->initFromEntity($simulator->getStrategy());
        $parameters = json_decode($simulatorStrategyData->getParameters(), true);
        $parameters[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS] = [$weekday];
        $simulatorStrategyData->setParameters(json_encode($parameters));

        $strategy = $this->simulationStrategyService->createByData($simulatorStrategyData);

        $simulatorData->setCashBox(100.0);
        $simulatorData->setIdentifier($simulatorData->getIdentifier() . '_' . $weekday->name);
        $simulatorData->setStrategy($strategy);
        $simulatorData->setFixtures([]);
        $simulatorData->setPlacements([]);
        $simulatorData->setCurrentIn(1.0);

        return $this->simulatorService->createByData($simulatorData);
    }
}
