<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Simulator\Data;


class SimulatorFilterData
{
    private bool $excludeNegative = true;
    private ?int $weekDay = null;
    private bool $excludeNsb = true;
    private ?array $variant = null;
    private ?float $minCashBox = null;
    private ?float $maxCashBox = null;
    private ?int $minBets = null;
    private ?int $maxBets = null;
    private int $maxResults = 20;
    private int $offset = 0;

    public function isExcludeNegative(): bool
    {
        return $this->excludeNegative;
    }

    public function setExcludeNegative(bool $excludeNegative): SimulatorFilterData
    {
        $this->excludeNegative = $excludeNegative;
        return $this;
    }

    public function getVariant(): ?array
    {
        return $this->variant;
    }

    public function setVariant(?array $variant): SimulatorFilterData
    {
        $this->variant = $variant;
        return $this;
    }

    public function getMinCashBox(): ?float
    {
        return $this->minCashBox;
    }

    public function setMinCashBox(?float $minCashBox): SimulatorFilterData
    {
        $this->minCashBox = $minCashBox;
        return $this;
    }

    public function getMaxCashBox(): ?float
    {
        return $this->maxCashBox;
    }

    public function setMaxCashBox(?float $maxCashBox): SimulatorFilterData
    {
        $this->maxCashBox = $maxCashBox;
        return $this;
    }

    public function getMinBets(): ?int
    {
        return $this->minBets;
    }

    public function setMinBets(?int $minBets): SimulatorFilterData
    {
        $this->minBets = $minBets;
        return $this;
    }

    public function getMaxBets(): ?int
    {
        return $this->maxBets;
    }

    public function setMaxBets(?int $maxBets): SimulatorFilterData
    {
        $this->maxBets = $maxBets;
        return $this;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function setMaxResults(int $maxResults): SimulatorFilterData
    {
        $this->maxResults = $maxResults;
        return $this;
    }

    public function getWeekDay(): ?int
    {
        return $this->weekDay;
    }

    public function setWeekDay(?int $weekDay): SimulatorFilterData
    {
        $this->weekDay = $weekDay;

        return $this;
    }

    public function isExcludeNsb(): bool
    {
        return $this->excludeNsb;
    }

    public function setExcludeNsb(bool $excludeNsb): SimulatorFilterData
    {
        $this->excludeNsb = $excludeNsb;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): SimulatorFilterData
    {
        $this->offset = $offset;
        return $this;
    }
}
