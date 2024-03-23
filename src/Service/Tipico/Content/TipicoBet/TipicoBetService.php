<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use Exception;

class TipicoBetService
{
    public function __construct(
        private readonly TipicoBetRepository $repository,
        private readonly TipicoBetFactory $factory
    )
    {
    }

    public function createByData(TipicoBetData $data): TipicoBet
    {
        $tipicoBet = $this->factory->createByData($data);
        $this->repository->save($tipicoBet);
        return $tipicoBet;
    }

    public function update(TipicoBet $tipicoBet, TipicoBetData $data): TipicoBet
    {
        $tipicoBet = $this->factory->mapData($data, $tipicoBet);
        $this->repository->save($tipicoBet);
        return $tipicoBet;
    }

    /**
     * @return TipicoBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByTipicoId(int $tipicoId): ?TipicoBet
    {
        return $this->repository->findOneBy(['tipicoId' => $tipicoId]);
    }

    /**
     * @return TipicoBet[]
     */
    public function findAllUndecoratedMatches(): array
    {
        return $this->repository->findAllUndecoratedMatches();
    }

    /**
     * @return TipicoBet[]
     */
    public function findUpcomingEventsByRange(
        float $min,
        float $max,
        string $targetOddColumn,
        int $limit = 5
    ): array {
        return $this->repository->findUpcomingEventsByRange($min, $max, $targetOddColumn, $limit);
    }

    /**
     * @return TipicoBet[]
     * @throws Exception
     */
    public function getFixtureForUpcomingFixturesByFilterCount(Simulator $simulator): array
    {
        $filter = $this->createBaseFilterForSimulator($simulator);
        $filter->setHasTimeFrame(true);

        $result = $this->repository->getFixtureByFilter($filter);
        if (!is_array($result)){
            throw new Exception('Expected array');
        }

        return $result;
    }

    public function getFixtureForSimulatorByFilterCount(Simulator $simulator): int
    {
        $filter = $this->createBaseFilterForSimulator($simulator);
        $filter->setCountRequest(true);

        $result = $this->repository->getFixtureByFilter($filter);
        if (!is_numeric($result)){
            throw new Exception('Expected int');
        }

        return $result;
    }

    /**
     * @return TipicoBet[]
     * @throws Exception
     */
    public function getFixtureForSimulatorByFilter(Simulator $simulator): array
    {
        $filter = $this->createBaseFilterForSimulator($simulator);

        $result = $this->repository->getFixtureByFilter($filter);
        if (!is_array($result)){
            throw new Exception('Expected array');
        }

        return $result;
    }

    private function createBaseFilterForSimulator(Simulator $simulator): TipicoBetFilter
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);

        $searchBeton = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
        $targetBeton = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
        $min = (float)$parameters[AbstractSimulationProcessor::PARAMETER_MIN];
        $max = (float)$parameters[AbstractSimulationProcessor::PARAMETER_MAX];
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

        if (
            $searchBeton === BetOn::OVER ||
            $searchBeton === BetOn::UNDER ||
            $targetBeton === BetOn::OVER ||
            $targetBeton === BetOn::UNDER)
        {
            $filter->setIncludeOverUnder(true);
            $filter->setTargetValue((float) $parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON_TARGET]);
        }

        if (
            $searchBeton === BetOn::BOTH_TEAMS_SCORE ||
            $searchBeton === BetOn::BOTH_TEAMS_SCORE_NOT ||
            $targetBeton === BetOn::BOTH_TEAMS_SCORE ||
            $targetBeton === BetOn::BOTH_TEAMS_SCORE_NOT)
        {
            $filter->setIncludeBothTeamsScore(true);
        }

        return $filter;
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
}
