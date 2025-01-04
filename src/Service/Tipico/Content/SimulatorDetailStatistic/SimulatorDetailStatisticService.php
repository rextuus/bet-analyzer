<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\SimulatorDetailStatistic;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use DateTime;

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
        $simulatorDetailStatistic->setEdited(new DateTime('now'));
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

    /**
     * @return array<SimulatorDetailStatistic>
     */
    public function findByWeekdayOrderedDesc(Weekday $weekday): array
    {
        return $this->repository->findByWeekdayOrderedDesc($weekday);
    }
}
