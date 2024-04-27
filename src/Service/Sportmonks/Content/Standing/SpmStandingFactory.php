<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Standing;

use App\Entity\Spm\SpmStanding;
use App\Service\Sportmonks\Content\Standing\Data\SpmStandingData;


class SpmStandingFactory
{
    public function createByData(SpmStandingData $data): SpmStanding
    {
        $spmStanding = $this->createNewInstance();
        $this->mapData($data, $spmStanding);
        return $spmStanding;
    }

    public function mapData(SpmStandingData $data, SpmStanding $spmStanding): SpmStanding
    {
        $spmStanding->setApiId($data->getApiId());
        $spmStanding->setParticipantApiId($data->getParticipantApiId());
        $spmStanding->setLeagueApiId($data->getLeagueApiId());
        $spmStanding->setSeasonApiId($data->getSeasonApiId());
        $spmStanding->setStageApiId($data->getStageApiId());
        $spmStanding->setRoundApiId($data->getRoundApiId());
        $spmStanding->setPosition($data->getPosition());
        $spmStanding->setPoints($data->getPoints());
        $spmStanding->setResult($data->getResult());

        return $spmStanding;
    }

    private function createNewInstance(): SpmStanding
    {
        return new SpmStanding();
    }
}
