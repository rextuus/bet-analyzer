<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season\Statistic;

use App\Entity\Spm\SeasonStatistic;
use App\Service\Sportmonks\Content\Season\Statistic\Data\SeasonStatisticData;


class SeasonStatisticFactory
{
    public function createByData(SeasonStatisticData $data): SeasonStatistic
    {
        $seasonStatistic = $this->createNewInstance();
        $this->mapData($data, $seasonStatistic);
        return $seasonStatistic;
    }

    public function mapData(SeasonStatisticData $data, SeasonStatistic $seasonStatistic): SeasonStatistic
    {
        $seasonStatistic->setSeasonApiId($data->getSeasonApiId());
        $seasonStatistic->setTeams($data->getTeams());
        $seasonStatistic->setLeague($data->getLeague());
        $seasonStatistic->setYear($data->getYear());
        $seasonStatistic->setMatchDays($data->getMatchDays());
        $seasonStatistic->setDecoratedFixtures($data->getDecoratedFixtures());
        $seasonStatistic->setStage($data->getStage());
        $seasonStatistic->setIsRegularSeason($data->isRegularSeason());
        $seasonStatistic->setIsFaulty($data->isFaulty());
        $seasonStatistic->setIsReliable($data->isReliable());
        $seasonStatistic->setExpectedMatchDays($data->getExpectedMatchDays());
        $seasonStatistic->setExpectedMatchDaysAlternative($data->getExpectedMatchDaysAlternative());
        $seasonStatistic->setNoStandingsAvailable($data->isNoStandingsAvailable());
        $seasonStatistic->setActuallyBetDecorated($data->getActuallyBetDecorated());
        $seasonStatistic->setManuallyConfirmed($data->isManuallyConfirmed());

        return $seasonStatistic;
    }

    private function createNewInstance(): SeasonStatistic
    {
        return new SeasonStatistic();
    }
}
