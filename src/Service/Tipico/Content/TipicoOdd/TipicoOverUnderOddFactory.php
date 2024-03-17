<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd;

use App\Entity\TipicoOverUnderOdd;
use App\Service\Tipico\Content\TipicoOdd\Data\TipicoOverUnderOddData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoOverUnderOddFactory
{
    public function createByData(TipicoOverUnderOddData $data): TipicoOverUnderOdd
    {
        $tipicoOdd = $this->createNewInstance();
        $this->mapData($data, $tipicoOdd);
        return $tipicoOdd;
    }

    public function mapData(TipicoOverUnderOddData $data, TipicoOverUnderOdd $tipicoOdd): TipicoOverUnderOdd
    {
        $tipicoOdd->setBet($data->getBet());
        $tipicoOdd->setOverValue($data->getOver());
        $tipicoOdd->setUnderValue($data->getUnder());
        $tipicoOdd->setTargetValue($data->getTarget());

        return $tipicoOdd;
    }

    private function createNewInstance(): TipicoOverUnderOdd
    {
        return new TipicoOverUnderOdd();
    }
}
