<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\OverUnderOdd;

use App\Entity\BettingProvider\TipicoOverUnderOdd;
use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\Data\TipicoOverUnderOddData;


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
