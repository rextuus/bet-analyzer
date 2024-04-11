<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\Simulation\AdditionalProcessors\NegativeSeriesProcessor;
use App\Service\Tipico\Simulation\Data\ProcessResult;
use DateTime;

class HeadToHeadStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'head_to_head';

    public function __construct(
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly NegativeSeriesProcessor $negativeSeriesProcessor,
    )
    {
        parent::__construct(
            $placementService,
            $simulatorService,
            $simulationStrategyService,
            $tipicoBetService,
            $negativeSeriesProcessor
        );
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }

    public function calculate(Simulator $simulator, array $fixtures, array $parameters): ProcessResult
    {
        $targetBetOn = Beton::from($parameters[self::PARAMETER_TARGET_BET_ON]);

        $placementData = [];
        $fixturesActuallyUsed = [];
        foreach ($fixtures as $fixture) {
            $isWon = false;
            $value = 1.0;

            if ($targetBetOn === BetOn::H2H_HOME) {
                $value = $fixture->getTipicoHeadToHeadScore()->getHomeTeamValue();

                if ($fixture->getResult() === BetOn::HOME) {
                    $isWon = true;
                }
            }

            if ($targetBetOn === BetOn::H2H_AWAY) {
                $value = $fixture->getTipicoHeadToHeadScore()->getAwayTeamValue();

                if ($fixture->getResult() === BetOn::AWAY) {
                    $isWon = true;
                }
            }

            // case draw => get money back by setting win == input
            if ($fixture->getResult() === BetOn::DRAW) {
                $value = 1.0;
                $isWon = true;
            }

            $placementData[] = $this->createPlacement(
                [$fixture],
                1.0,
                $value,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp()/1000),
                $isWon,
                $simulator
            );

            $fixturesActuallyUsed[] = $fixture;
        }

        $result = new ProcessResult();
        $result->setPlacementData($placementData);
        $result->setFixturesActuallyUsed($fixturesActuallyUsed);

        return $result;
    }
}
