<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulator;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BothTeamsScoreStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'both_teams_score';

    public function __construct(
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly TelegramMessageService $telegramMessageService,
        private readonly TipicoBetSimulator $tipicoBetSimulator,
    )
    {
        parent::__construct($placementService, $simulatorService, $simulationStrategyService, $tipicoBetService);
    }

    public function calculate(Simulator $simulator): PlacementContainer
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);

        $fixtures = $this->getFixtureForSimulatorBySearchAndTarget($simulator);

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


            $placementData[] = $this->tipicoBetSimulator->createPlacement(
                [$fixture],
                1.0,
                $usedOdd,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000),
                $isWon,
                $simulator
            );

            $fixturesActuallyUsed[] = $fixture;
        }

        // store changes
        $container = $this->storePlacementsToDatabase($placementData);
        $this->storeSimulatorChangesToDatabase($simulator, $fixturesActuallyUsed, $container);

        return $container;
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }
}
