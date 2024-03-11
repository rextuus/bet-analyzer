<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorService
{
    public function __construct(private readonly SimulatorRepository $repository, private readonly SimulatorFactory $factory)
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
     * @param array<string, mixed> $data
     * @return Simulator[]
     */
    public function findByFilter(array $data): array
    {
        return $this->repository->findByFilter($data);
    }
}
