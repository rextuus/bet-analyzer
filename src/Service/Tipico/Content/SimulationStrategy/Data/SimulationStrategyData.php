<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulationStrategy\Data;

use App\Entity\BettingProvider\SimulationStrategy;
use App\Service\Tipico\Content\SimulationStrategy\AdditionalProcessingIdent;


class SimulationStrategyData
{
    private string $identifier;
    private mixed $parameters;
    private ?AdditionalProcessingIdent $processingIdent;

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

    public function getProcessingIdent(): ?AdditionalProcessingIdent
    {
        return $this->processingIdent;
    }

    public function setProcessingIdent(?AdditionalProcessingIdent $processingIdent): SimulationStrategyData
    {
        $this->processingIdent = $processingIdent;
        return $this;
    }

    public function initFromEntity(SimulationStrategy $strategy): SimulationStrategyData
    {
        $this->setIdentifier($strategy->getIdentifier());
        $this->setParameters($strategy->getParameters());
        $this->setProcessingIdent($strategy->getAdditionalProcessingIdent());

        return $this;
    }
}
