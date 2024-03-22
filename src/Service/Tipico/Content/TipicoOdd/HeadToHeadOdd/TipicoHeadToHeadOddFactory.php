<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd;

use App\Entity\TipicoHeadToHeadOdd;
use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\Data\TipicoHeadToHeadOddData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoHeadToHeadOddFactory
{
    public function createByData(TipicoHeadToHeadOddData $data): TipicoHeadToHeadOdd
    {
        $tipicoHeadToHeadOdd = $this->createNewInstance();
        $this->mapData($data, $tipicoHeadToHeadOdd);
        return $tipicoHeadToHeadOdd;
    }

    public function mapData(TipicoHeadToHeadOddData $data, TipicoHeadToHeadOdd $tipicoHeadToHeadOdd): TipicoHeadToHeadOdd
    {
        $tipicoHeadToHeadOdd->setBet($data->getBet());
        $tipicoHeadToHeadOdd->setHomeTeamValue($data->getHomeTeamValue());
        $tipicoHeadToHeadOdd->setAwayTeamValue($data->getAwayTeamValue());

        return $tipicoHeadToHeadOdd;
    }

    private function createNewInstance(): TipicoHeadToHeadOdd
    {
        return new TipicoHeadToHeadOdd();
    }
}
