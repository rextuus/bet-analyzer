<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture;

use App\Entity\Spm\SpmFixture;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;


class SpmFixtureFactory
{
    public function createByData(SpmFixtureData $data): SpmFixture
    {
        $spmFixture = $this->createNewInstance();
        $this->mapData($data, $spmFixture);
        return $spmFixture;
    }

    public function mapData(SpmFixtureData $data, SpmFixture $spmFixture): SpmFixture
    {
        $spmFixture->setApiId($data->getApiId());
        $spmFixture->setStartingAtTimestamp($data->getStartingAtTimestamp());
        $spmFixture->setLeagueApiId($data->getLeagueApiId());
        $spmFixture->setStartingAt($data->getStartingAt());
        $spmFixture->setSeasonApiId($data->getSeasonApiId());
        $spmFixture->setResultInfo($data->getResultInfo());
        $spmFixture->setRoundApiId($data->getRoundApiId());
        $spmFixture->setOddDecorated($data->isOddDecorated());
        $spmFixture->setScoreDecorated($data->isScoreDecorated());

        return $spmFixture;
    }

    private function createNewInstance(): SpmFixture
    {
        return new SpmFixture();
    }
}
