<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Odd;

use App\Entity\Spm\SpmOdd;
use App\Service\Sportmonks\Content\Odd\Data\SpmOddData;


class SpmOddFactory
{
    public function createByData(SpmOddData $data): SpmOdd
    {
        $spmOdd = $this->createNewInstance();
        $this->mapData($data, $spmOdd);
        return $spmOdd;
    }

    public function mapData(SpmOddData $data, SpmOdd $spmOdd): SpmOdd
    {
        $spmOdd->setLabel($data->getLabel());
        $spmOdd->setName($data->getName());
        $spmOdd->setMarketDescription($data->getMarketDescription());
        $spmOdd->setApiId($data->getApiId());
        $spmOdd->setValue($data->getValue());
        $spmOdd->setBookmakerApiId($data->getBookmakerApiId());
        $spmOdd->setFixtureApiId($data->getFixtureApiId());

        return $spmOdd;
    }

    private function createNewInstance(): SpmOdd
    {
        return new SpmOdd();
    }
}
