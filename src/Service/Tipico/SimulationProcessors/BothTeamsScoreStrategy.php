<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\BettingProvider\Simulator;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\Simulation\AdditionalProcessors\NegativeSeriesProcessor;
use App\Service\Tipico\Simulation\AdditionalProcessors\RandomPlacementProcessor;
use App\Service\Tipico\Simulation\Data\ProcessResult;
use DateTime;


class BothTeamsScoreStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'both_teams_score';

    public function __construct(
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly NegativeSeriesProcessor $negativeSeriesProcessor,
        protected readonly RandomPlacementProcessor $randomPlacementProcessor,
    )
    {
        parent::__construct(
            $placementService,
            $simulatorService,
            $simulationStrategyService,
            $tipicoBetService,
            $negativeSeriesProcessor,
            $this->randomPlacementProcessor
        );
    }

    public function calculate(Simulator $simulator, array $fixtures, array $parameters): ProcessResult
    {
        $targetBeton = BetOn::from($parameters[self::PARAMETER_TARGET_BET_ON]);

        $placementData = [];
        $fixturesActuallyUsed = [];
        foreach ($fixtures as $fixture) {
            $odd = $fixture->getTipicoBothTeamsScoreBet();
            if (!$odd){
                continue;
            }

            $usedOdd = $odd->getConditionTrueValue();

            $isWon = $fixture->getEndScoreHome() > 0 && $fixture->getEndScoreAway() > 0;
            if ($targetBeton === BetOn::BOTH_TEAMS_SCORE_NOT){
                $isWon = !$isWon;
                $usedOdd = $odd->getConditionFalseValue();
            }


            $placementData[] = $this->createPlacement(
                [$fixture],
                1.0,
                $usedOdd,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000),
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

    public function getIdentifier(): string
    {
        return self::IDENT;
    }
}
