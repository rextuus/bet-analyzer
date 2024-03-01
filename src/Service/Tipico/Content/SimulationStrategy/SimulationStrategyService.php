<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulationStrategy;

use App\Entity\SimulationStrategy;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStrategyService
{
    public function __construct(private readonly SimulationStrategyRepository $repository, private readonly SimulationStrategyFactory $factory)
    {
    }

    public function createByData(SimulationStrategyData $data): SimulationStrategy
    {
        $simulationStrategy = $this->factory->createByData($data);
        $this->repository->save($simulationStrategy);
        return $simulationStrategy;
    }

    public function update(SimulationStrategy $simulationStrategy, SimulationStrategyData $data): SimulationStrategy
    {
        $simulationStrategy = $this->factory->mapData($data, $simulationStrategy);
        $this->repository->save($simulationStrategy);
        return $simulationStrategy;
    }

    /**
     * @return SimulationStrategy[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }
}
