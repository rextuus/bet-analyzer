<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\SimulatorDetailStatistic;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;

class SimulatorDetailStatisticFactory
{
    public function createByData(SimulatorDetailStatisticData $data): SimulatorDetailStatistic
    {
        $simulatorDetailStatistic = $this->createNewInstance();
        $this->mapData($data, $simulatorDetailStatistic);
        return $simulatorDetailStatistic;
    }

    public function mapData(
        SimulatorDetailStatisticData $data,
        SimulatorDetailStatistic $simulatorDetailStatistic
    ): SimulatorDetailStatistic {
        $simulatorDetailStatistic->setExample($data->getExample());

        return $simulatorDetailStatistic;
    }

    private function createNewInstance(): SimulatorDetailStatistic
    {
        return new SimulatorDetailStatistic();
    }
}
