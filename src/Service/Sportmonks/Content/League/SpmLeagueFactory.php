<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\League;

use App\Entity\SpmLeague;
use App\Service\Sportmonks\Content\League\Data\SpmLeagueData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmLeagueFactory
{
    public function createByData(SpmLeagueData $data): SpmLeague
    {
        $spmLeague = $this->createNewInstance();
        $this->mapData($data, $spmLeague);
        return $spmLeague;
    }

    public function mapData(SpmLeagueData $data, SpmLeague $spmLeague): SpmLeague
    {
        $spmLeague->setApiId($data->getApiId());
        $spmLeague->setShort($data->getShort());
        $spmLeague->setCountry($data->getCountry());
        $spmLeague->setName($data->getName());

        return $spmLeague;
    }

    private function createNewInstance(): SpmLeague
    {
        return new SpmLeague();
    }
}
