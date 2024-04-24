<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\TipicoBet;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;


/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBetFactory
{
    public function createByData(TipicoBetData $data): TipicoBet
    {
        $tipicoBet = $this->createNewInstance();
        $this->mapData($data, $tipicoBet);
        return $tipicoBet;
    }

    public function mapData(TipicoBetData $data, TipicoBet $tipicoBet): TipicoBet
    {
        $tipicoBet->setTipicoId($data->getTipicoId());
        $tipicoBet->setTipicoHomeTeamId($data->getTipicoHomeTeamId());
        $tipicoBet->setTipicoAwayTeamId($data->getTipicoAwayTeamId());
        $tipicoBet->setHomeTeamName($data->getHomeTeamName());
        $tipicoBet->setAwayTeamName($data->getAwayTeamName());
        $tipicoBet->setStartAtTimeStamp($data->getStartAtTimeStamp());
        $tipicoBet->setOddHome($data->getOddHome());
        $tipicoBet->setOddDraw($data->getOddDraw());
        $tipicoBet->setOddAway($data->getOddAway());
        $tipicoBet->setEndScoreHome($data->getEndScoreHome());
        $tipicoBet->setEndScoreAway($data->getEndScoreAway());
        $tipicoBet->setFinished($data->isFinished());
        $tipicoBet->setResult($data->getResult());
        $tipicoBet->setSportRadarId($data->getSportRadarId());

        return $tipicoBet;
    }

    private function createNewInstance(): TipicoBet
    {
        return new TipicoBet();
    }
}
