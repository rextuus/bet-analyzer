<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulationStrategy;

use App\Entity\BettingProvider\SimulationStrategy;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;


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
        $simulationStrategy->setAdditionalProcessingIdent($data->getProcessingIdent());

        return $simulationStrategy;
    }

    private function createNewInstance(): SimulationStrategy
    {
        return new SimulationStrategy();
    }
}
