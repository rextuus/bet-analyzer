<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetFilter;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;

class AbstractSimulationProcessor
{
    public const PARAMETER_SEARCH_BET_ON = 'searchBetOn';
    public const PARAMETER_TARGET_BET_ON = 'targetBetOn';
    public const PARAMETER_MIN = 'min';
    public const PARAMETER_MAX = 'max';

    public function __construct(
        private readonly TipicoPlacementService $placementService,
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly TipicoBetService $tipicoBetService,
    )
    {
    }

    /**
     * @param TipicoPlacementData[] $dataObjects
     */
    protected function storePlacementsToDatabase(array $dataObjects): PlacementContainer
    {
        $placements = [];
        $cashBoxChange = 0.0;
        foreach ($dataObjects as $data) {
            $placement = $this->placementService->createByData($data);
            $placements[] = $placement;
            $cashBoxChange = $cashBoxChange - $placement->getInput();
            if ($placement->isWon()) {
                $cashBoxChange = $cashBoxChange + ($placement->getInput() * $placement->getValue());
            }
        }

        $container = new PlacementContainer();
        $container->setPlacements($placements);
        $container->setCashBoxChange($cashBoxChange);

        return $container;
    }

    /**
     * @param TipicoBet[] $fixturesActuallyUsed
     */
    protected function storeSimulatorChangesToDatabase(
        Simulator $simulator,
        array $fixturesActuallyUsed,
        PlacementContainer $container
    ): void
    {
        $simulatorData = (new SimulatorData())->initFromEntity($simulator);
        $simulatorData->setFixtures($fixturesActuallyUsed);
        $simulatorData->setPlacements($container->getPlacements());
        $simulatorData->setCashBox($simulator->getCashBox() + $container->getCashBoxChange());

        $this->simulatorService->update($simulator, $simulatorData);
    }

    /**
     * @return int[]
     */
    protected function getUsedFixtureIds(Simulator $simulator): array
    {
        return array_map(
            function (TipicoBet $tipicoBet) {
                return $tipicoBet->getId();
            },
            $simulator->getFixtures()->toArray()
        );
    }

    public function isHighCalculationAmount(Simulator $simulator): bool
    {
        $result = $this->getFixtureForSimulatorBySearchAndTarget($simulator, true);
        if (is_numeric($result) && $result > 100) {
            return true;
        }

        return false;
    }

    /**
     * @return TipicoBet[]|int
     */
    public function getFixtureForSimulatorBySearchAndTarget(Simulator $simulator, $isCount = false): array|int
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);

        $searchBeton = BetOn::from($parameters[self::PARAMETER_SEARCH_BET_ON]);
        $targetBeton = BetOn::from($parameters[self::PARAMETER_TARGET_BET_ON]);
        $min = (float)$parameters[self::PARAMETER_MIN];
        $max = (float)$parameters[self::PARAMETER_MAX];
        $usedFixtures = $this->getUsedFixtureIds($simulator);
        if ($usedFixtures === []) {
            $usedFixtures[] = -1;
        }

        $filter = new TipicoBetFilter();
        $filter->setMin($min);
        $filter->setMax($max);
        $filter->setAlreadyUsedFixtureIds($usedFixtures);

        match ($searchBeton) {
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY =>
            $searchTableAlias = TipicoBetFilter::TABLE_ALIAS_TIPICO_BET,
            BetOn::OVER, BetOn::UNDER =>
            $searchTableAlias = TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_OVER_UNDER,
            BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT =>
            $searchTableAlias = TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_BOTH_SCORE,
            BetOn::H2H_HOME, BetOn::H2H_AWAY =>
            $searchTableAlias = TipicoBetFilter::TABLE_ALIAS_TIPICO_HEAD_TO_HEAD,
        };

        match ($searchBeton) {
            BetOn::HOME => $searchOddColumn = 'oddHome',
            BetOn::DRAW => $searchOddColumn = 'oddDraw',
            BetOn::AWAY => $searchOddColumn = 'oddAway',
            BetOn::OVER => $searchOddColumn = 'overValue',
            BetOn::UNDER => $searchOddColumn = 'underValue',
            BetOn::BOTH_TEAMS_SCORE => $searchOddColumn = 'conditionTrueValue',
            BetOn::BOTH_TEAMS_SCORE_NOT => $searchOddColumn = 'conditionFalseValue',
            BetOn::H2H_HOME => $searchOddColumn = 'homeTeamValue',
            BetOn::H2H_AWAY => $searchOddColumn = 'awayTeamValue',
        };

        $filter->setSearchTableAlias($searchTableAlias);
        $filter->setSearchOddColumn($searchOddColumn);
        $filter->setCountRequest($isCount);

        if (
            $searchBeton === BetOn::OVER ||
            $searchBeton === BetOn::UNDER ||
            $targetBeton === BetOn::OVER ||
            $targetBeton === BetOn::UNDER)
        {
            $filter->setIncludeOverUnder(true);
        }

        if (
            $searchBeton === BetOn::BOTH_TEAMS_SCORE ||
            $searchBeton === BetOn::BOTH_TEAMS_SCORE_NOT ||
            $targetBeton === BetOn::BOTH_TEAMS_SCORE ||
            $targetBeton === BetOn::BOTH_TEAMS_SCORE_NOT)
        {
            $filter->setIncludeBothTeamsScore(true);
        }

        return $this->tipicoBetService->getFixtureByFilter($filter);
    }

    /**
     * @deprecated
     */
    protected function storeSimulatorStrategyChanges(Simulator $simulator, float $compensationIn): void
    {
        $strategy = $simulator->getStrategy();
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);
        $parameters[CompensateLossStrategy::PARAMETER_COMPENSATION] = $compensationIn;

        $simulationStrategyData = (new SimulationStrategyData())->initFromEntity($strategy);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->simulationStrategyService->update($strategy, $simulationStrategyData);
    }
}
