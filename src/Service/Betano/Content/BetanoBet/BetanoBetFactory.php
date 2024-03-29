<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoBet;

use App\Entity\BetanoBet;
use App\Service\Betano\Content\BetanoBet\Data\BetanoBetData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoBetFactory
{
    public function createByData(BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->createNewInstance();
        $this->mapData($data, $betanoBet);
        return $betanoBet;
    }

    public function mapData(BetanoBetData $data, BetanoBet $betanoBet): BetanoBet
    {
        $betanoBet->setBetanoId($data->getBetanoId());
        $betanoBet->setOddHome($data->getOddHome());
        $betanoBet->setOddDraw($data->getOddDraw());
        $betanoBet->setOddAway($data->getOddAway());
        $betanoBet->setHomeTeamName($data->getHomeTeamName());
        $betanoBet->setAwayTeamName($data->getAwayTeamName());
        $betanoBet->setStartAtTimeStamp($data->getStartAtTimeStamp());
        $betanoBet->setUrl($data->getUrl());

        return $betanoBet;
    }

    private function createNewInstance(): BetanoBet
    {
        return new BetanoBet();
    }
}
