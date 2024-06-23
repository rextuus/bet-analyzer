<?php
declare(strict_types=1);

namespace App\Service\Tipico\Simulation\Data;

use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;


class AdditionalProcessResult
{
    /**
     * @var TipicoPlacementData[]
     */
    private array $placementData = [];

    private int $currentNegativeSeries = 0;
    private bool $processedNegativeSeries = false;
    private bool $processesRandomInput = false;

    public function getPlacementData(): array
    {
        return $this->placementData;
    }

    public function setPlacementData(array $placementData): AdditionalProcessResult
    {
        $this->placementData = $placementData;
        return $this;
    }

    public function getCurrentNegativeSeries(): int
    {
        return $this->currentNegativeSeries;
    }

    public function setCurrentNegativeSeries(int $currentNegativeSeries): AdditionalProcessResult
    {
        $this->currentNegativeSeries = $currentNegativeSeries;
        return $this;
    }

    public function isProcessedNegativeSeries(): bool
    {
        return $this->processedNegativeSeries;
    }

    public function setProcessedNegativeSeries(bool $processedNegativeSeries): AdditionalProcessResult
    {
        $this->processedNegativeSeries = $processedNegativeSeries;
        return $this;
    }

    public function isProcessesRandomInput(): bool
    {
        return $this->processesRandomInput;
    }

    public function setProcessesRandomInput(bool $processesRandomInput): AdditionalProcessResult
    {
        $this->processesRandomInput = $processesRandomInput;
        return $this;
    }
}
