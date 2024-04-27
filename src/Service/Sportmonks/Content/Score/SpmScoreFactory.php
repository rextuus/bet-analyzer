<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Score;

use App\Entity\Spm\SpmScore;
use App\Service\Sportmonks\Content\Score\Data\SpmScoreData;


class SpmScoreFactory
{
    public function createByData(SpmScoreData $data): SpmScore
    {
        $spmScore = $this->createNewInstance();
        $this->mapData($data, $spmScore);
        return $spmScore;
    }

    public function mapData(SpmScoreData $data, SpmScore $spmScore): SpmScore
    {
        $spmScore->setApiId($data->getApiId());
        $spmScore->setFixtureApiId($data->getFixtureApiId());
        $spmScore->setParticipantApiId($data->getParticipantApiId());
        $spmScore->setGoals($data->getGoals());
        $spmScore->setDescription($data->getDescription());
        $spmScore->setParticipant($data->getParticipant());

        return $spmScore;
    }

    private function createNewInstance(): SpmScore
    {
        return new SpmScore();
    }
}
