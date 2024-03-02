<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulationStrategy\Data;

use App\Entity\SimulationStrategy;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStrategyData
{
    private string $identifier;
    private mixed $parameters;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): SimulationStrategyData
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getParameters(): mixed
    {
        return $this->parameters;
    }

    public function setParameters(mixed $parameters): SimulationStrategyData
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function initFromEntity(SimulationStrategy $strategy): SimulationStrategyData
    {
        $this->setIdentifier($strategy->getIdentifier());
        $this->setParameters($strategy->getParameters());

        return $this;
    }
}
