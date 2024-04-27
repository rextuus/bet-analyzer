<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;


class SimulatorFavoriteListFactory
{
    public function createByData(SimulatorFavoriteListData $data): SimulatorFavoriteList
    {
        $simulatorFavoriteList = $this->createNewInstance();
        $this->mapData($data, $simulatorFavoriteList);
        return $simulatorFavoriteList;
    }

    public function mapData(SimulatorFavoriteListData $data, SimulatorFavoriteList $simulatorFavoriteList): SimulatorFavoriteList
    {
        $simulatorFavoriteList->setIdentifier($data->getIdentifier());
        $simulatorFavoriteList->setCreated($data->getCreated());
        $simulatorFavoriteList->setTotalCashBox($data->getTotalCashBox());
        $simulatorFavoriteList->setBets($data->getBets());
        foreach ($data->getSimulators() as $simulator){
            $simulatorFavoriteList->addSimulator($simulator);
        }

        return $simulatorFavoriteList;
    }

    private function createNewInstance(): SimulatorFavoriteList
    {
        return new SimulatorFavoriteList();
    }
}
