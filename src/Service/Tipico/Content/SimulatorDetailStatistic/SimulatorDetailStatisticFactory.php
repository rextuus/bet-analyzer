<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\SimulatorDetailStatistic;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;
use DateTime;

class SimulatorDetailStatisticFactory
{
    public function createByData(SimulatorDetailStatisticData $data): SimulatorDetailStatistic
    {
        $simulatorDetailStatistic = $this->createNewInstance();
        $simulatorDetailStatistic->setCreationDate(new DateTime('now'));
        $this->mapData($data, $simulatorDetailStatistic);
        return $simulatorDetailStatistic;
    }

    public function mapData(
        SimulatorDetailStatisticData $data,
        SimulatorDetailStatistic $simulatorDetailStatistic
    ): SimulatorDetailStatistic {
        $simulatorDetailStatistic->setSimulator($data->getSimulator());

        $simulatorDetailStatistic->setMondayTotal($data->getMondayTotal());
        $simulatorDetailStatistic->setMondayAverage($data->getMondayAverage());
        $simulatorDetailStatistic->setTuesdayTotal($data->getTuesdayTotal());
        $simulatorDetailStatistic->setTuesdayAverage($data->getTuesdayAverage());
        $simulatorDetailStatistic->setWednesdayTotal($data->getWednesdayTotal());
        $simulatorDetailStatistic->setWednesdayAverage($data->getWednesdayAverage());
        $simulatorDetailStatistic->setThursdayTotal($data->getThursdayTotal());
        $simulatorDetailStatistic->setThursdayAverage($data->getThursdayAverage());
        $simulatorDetailStatistic->setFridayTotal($data->getFridayTotal());
        $simulatorDetailStatistic->setFridayAverage($data->getFridayAverage());
        $simulatorDetailStatistic->setSaturdayTotal($data->getSaturdayTotal());
        $simulatorDetailStatistic->setSaturdayAverage($data->getSaturdayAverage());
        $simulatorDetailStatistic->setSundayTotal($data->getSundayTotal());
        $simulatorDetailStatistic->setSundayAverage($data->getSundayAverage());

        return $simulatorDetailStatistic;
    }

    private function createNewInstance(): SimulatorDetailStatistic
    {
        return new SimulatorDetailStatistic();
    }
}
