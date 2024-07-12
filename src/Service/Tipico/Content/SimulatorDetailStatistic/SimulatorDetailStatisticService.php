<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\SimulatorDetailStatistic;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;

class SimulatorDetailStatisticService
{
    public function __construct(
        private readonly SimulatorDetailStatisticRepository $repository,
        private readonly SimulatorDetailStatisticFactory $factory
    ) {
    }

    public function createByData(SimulatorDetailStatisticData $data): SimulatorDetailStatistic
    {
        $simulatorDetailStatistic = $this->factory->createByData($data);
        $this->repository->save($simulatorDetailStatistic);
        return $simulatorDetailStatistic;
    }

    public function update(
        SimulatorDetailStatistic $simulatorDetailStatistic,
        SimulatorDetailStatisticData $data
    ): SimulatorDetailStatistic {
        $simulatorDetailStatistic = $this->factory->mapData($data, $simulatorDetailStatistic);
        $this->repository->save($simulatorDetailStatistic);
        return $simulatorDetailStatistic;
    }

    /**
     * @return SimulatorDetailStatistic[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }
}
