<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use DateTime;


class SimulatorService
{
    public function __construct(
        private readonly SimulatorRepository $repository,
        private readonly SimulatorFactory $factory,
        private readonly float $cashBoxLimit,
    )
    {
    }

    public function createByData(SimulatorData $data): Simulator
    {
        $simulator = $this->factory->createByData($data);
        $this->repository->save($simulator);
        return $simulator;
    }

    public function update(Simulator $simulator, SimulatorData $data): Simulator
    {
        $simulator = $this->factory->mapData($data, $simulator);
        $this->repository->save($simulator);
        return $simulator;
    }

    /**
     * @return Simulator[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @return Simulator[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @return int[]
     */
    public function findAllSimulatorIds(): array
    {
        return $this->repository->findAllById();
    }

    /**
     * @return Simulator[]
     */
    public function findByFilter(SimulatorFilterData $filter): array
    {
        return $this->repository->findByFilter($filter);
    }

    public function getSimulatorCashBoxDistribution(): array
    {
        $simulators = $this->repository->findWithPlacements();

        $total = count($simulators);

        $distribution = [];
        foreach ($simulators as $simulator){
            $value = floor($simulator->getCashBox());
            $from = floor($value/5);
            $key = $from;

            if (!array_key_exists($key, $distribution)){
                $distribution[$key] = 0;
            }
            $distribution[$key] = $distribution[$key] + 1;
        }
        ksort($distribution);

        $result = ['inactive' => 0];
        $active = 0;
        foreach ($distribution as $key => $value){
            $from = $key*5;
            $key = sprintf('%.0f - %.0f', $from, ($key+1) * 5);


            if ($from >= $this->cashBoxLimit){
                $result[$key] = $value;
                $active = $active + $value;
                continue;
            }
            $result['inactive'] = $result['inactive'] + $value;
        }

        return $result;
    }

    /**
     * @param string[] $strategyIdents
     * @return array<array<string, int>
     */
    public function findByStrategies(array $strategyIdents, array $additional): array
    {
        return $this->repository->findByStrategies($strategyIdents, $additional);
    }

    public function findBySimulatorsWithPlacements(SimulatorFavoriteList $simulatorFavoriteList, DateTime $from, DateTime $until)
    {
        $ids = [];
        foreach ($simulatorFavoriteList->getSimulators() as $simulator){
            $ids[] = $simulator->getId();
        }

        return $this->repository->findBySimulatorsWithPlacements($ids, $from, $until);
    }
}
