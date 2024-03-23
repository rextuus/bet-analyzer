<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\TipicoBetSimulator;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 * @deprecated
 */
class CompensateLossStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'compensate_loss';
    public const PARAMETER_DEFAULT_IN = 'default_in';
    public const PARAMETER_COMPENSATION = 'compensation';

    public function __construct(
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        private readonly TipicoBetSimulator $tipicoBetSimulator,
    )
    {
        parent::__construct($placementService, $simulatorService, $simulationStrategyService, $tipicoBetService);
    }

    public function calculate(Simulator $simulator): PlacementContainer
    {
        return new PlacementContainer();
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);

        $fixtures = $this->getFixtureForSimulatorBySearchAndTarget($simulator);

        $defaultIn = $parameters[self::PARAMETER_DEFAULT_IN];
        $compensationIn = $parameters[self::PARAMETER_COMPENSATION];

        $placementData = [];
        $fixturesActuallyUsed = [];

        foreach ($fixtures as $nr => $fixture) {
            $value = $this->tipicoBetSimulator->getOddValueByBeton($fixture, BetOn::from($parameters[self::PARAMETER_SEARCH_BET_ON]));

            $compensation = 0.0;
            if ($compensationIn > 0.0){
                $compensation = ($compensationIn) / ($value - 1.0);
            }
            $currentIn = $defaultIn + $compensation;

            $betIsWon = $fixture->getResult() === BetOn::from($parameters[self::PARAMETER_SEARCH_BET_ON]);

            $placementData[] = $this->tipicoBetSimulator->createPlacement(
              [$fixture],
                $currentIn,
                $value,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp()/1000),
                $betIsWon,
                $simulator
            );

            // loose => add to compensate in
            if (!$betIsWon){
                $compensationIn = $compensationIn + $currentIn;
            }else{
                $compensationIn = 0.0;
            }
            $fixturesActuallyUsed[] = $fixture;
        }

        // store changes
        $container = $this->storePlacementsToDatabase($placementData);
        $this->storeSimulatorChangesToDatabase($simulator, $fixturesActuallyUsed, $container);
        $this->storeSimulatorStrategyChanges($simulator, $compensationIn);
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }
}
