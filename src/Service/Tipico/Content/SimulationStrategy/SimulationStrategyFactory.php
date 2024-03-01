<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulationStrategy;

use App\Entity\SimulationStrategy;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStrategyFactory
{
    public function createByData(SimulationStrategyData $data): SimulationStrategy
    {
        $simulationStrategy = $this->createNewInstance();
        $this->mapData($data, $simulationStrategy);
        return $simulationStrategy;
    }

    public function mapData(SimulationStrategyData $data, SimulationStrategy $simulationStrategy): SimulationStrategy
    {
        $simulationStrategy->setParameters($data->getParameters());
        $simulationStrategy->setIdentifier($data->getIdentifier());

        return $simulationStrategy;
    }

    private function createNewInstance(): SimulationStrategy
    {
        return new SimulationStrategy();
    }
}
