<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Round;

use App\Entity\SpmRound;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmRoundFactory
{
    public function createByData(SpmRoundData $data): SpmRound
    {
        $spmRound = $this->createNewInstance();
        $this->mapData($data, $spmRound);
        return $spmRound;
    }

    public function mapData(SpmRoundData $data, SpmRound $spmRound): SpmRound
    {
        $spmRound->setName($data->getName());
        $spmRound->setStartingAt($data->getStartingAt());
        $spmRound->setSeasonApiId($data->getSeasonApiId());
        $spmRound->setApiId($data->getApiId());
        $spmRound->setEndingAt($data->getEndingAt());
        $spmRound->setLeagueApiId($data->getLeagueApiId());
        $spmRound->setEndingAt($data->getEndingAt());
        $spmRound->setFixturesComplete($data->isFixtureCompleted());
        $spmRound->setOddsComplete($data->isOddsCompleted());

        return $spmRound;
    }

    private function createNewInstance(): SpmRound
    {
        return new SpmRound();
    }
}
