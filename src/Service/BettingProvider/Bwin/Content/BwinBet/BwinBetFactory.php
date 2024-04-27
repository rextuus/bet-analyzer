<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Bwin\Content\BwinBet;

use App\Entity\BwinBet;
use App\Service\BettingProvider\Bwin\Content\BwinBet\Data\BwinBetData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BwinBetFactory
{
    public function createByData(BwinBetData $data): BwinBet
    {
        $bwinBet = $this->createNewInstance();
        $this->mapData($data, $bwinBet);
        return $bwinBet;
    }

    public function mapData(BwinBetData $data, BwinBet $bwinBet): BwinBet
    {
        $bwinBet->setBwinId($data->getBwinId());
        $bwinBet->setOddHome($data->getOddHome());
        $bwinBet->setOddDraw($data->getOddDraw());
        $bwinBet->setOddAway($data->getOddAway());
        $bwinBet->setHomeTeamName($data->getHomeTeamName());
        $bwinBet->setAwayTeamName($data->getAwayTeamName());
        $bwinBet->setStartAtTimeStamp($data->getStartAtTimeStamp());
        $bwinBet->setUrl($data->getUrl());
        $bwinBet->setSportRadarId($data->getSportRadarId());
        $bwinBet->setTipicoBet($data->getTipicoBet());

        return $bwinBet;
    }

    private function createNewInstance(): BwinBet
    {
        return new BwinBet();
    }
}
