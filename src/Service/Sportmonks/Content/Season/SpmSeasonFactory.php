<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season;

use App\Entity\Spm\SpmSeason;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;


class SpmSeasonFactory
{
    public function createByData(SpmSeasonData $data): SpmSeason
    {
        $spmSeason = $this->createNewInstance();
        $this->mapData($data, $spmSeason);
        return $spmSeason;
    }

    public function mapData(SpmSeasonData $data, SpmSeason $spmSeason): SpmSeason
    {
        $spmSeason->setApiId($data->getApiId());
        $spmSeason->setLeagueApiId($data->getLeagueApiId());
        $spmSeason->setName($data->getName());
        $spmSeason->setStartingAt($data->getStartingAt());
        $spmSeason->setEndingAt($data->getEndingAt());
        $spmSeason->setIsCurrent($data->isCurrent());
        $spmSeason->setFinished($data->isCurrent());
        $spmSeason->setFixtureDecorated($data->getFixtureDecorated());
        $spmSeason->setOddDecorated($data->getOddDecorated());
        $spmSeason->setExpectedFixtures($data->getExpectedFixtures());
        $spmSeason->setDisplayName($data->getDisplayName());

        return $spmSeason;
    }

    private function createNewInstance(): SpmSeason
    {
        return new SpmSeason();
    }
}
