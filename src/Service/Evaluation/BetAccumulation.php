<?php
declare(strict_types=1);

namespace App\Service\Evaluation;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class BetAccumulation
{
    private string $accumulationKey;
    /**
     * @var int[]
     */
    private array $oddIds;

    private float $minOdd;
    private float $maxOdd;
    private float $avgOdd;
    private float $medianOdd;

    public function getAccumulationKey(): string
    {
        return $this->accumulationKey;
    }

    public function setAccumulationKey(string $accumulationKey): BetAccumulation
    {
        $this->accumulationKey = $accumulationKey;
        return $this;
    }

    public function getOddIds(): array
    {
        return $this->oddIds;
    }

    public function setOddIds(array $oddIds): BetAccumulation
    {
        $this->oddIds = $oddIds;
        return $this;
    }

    public function getMinOdd(): float
    {
        return $this->minOdd;
    }

    public function setMinOdd(float $minOdd): BetAccumulation
    {
        $this->minOdd = $minOdd;
        return $this;
    }

    public function getMaxOdd(): float
    {
        return $this->maxOdd;
    }

    public function setMaxOdd(float $maxOdd): BetAccumulation
    {
        $this->maxOdd = $maxOdd;
        return $this;
    }

    public function getAvgOdd(): float
    {
        return $this->avgOdd;
    }

    public function setAvgOdd(float $avgOdd): BetAccumulation
    {
        $this->avgOdd = $avgOdd;
        return $this;
    }

    public function getMedianOdd(): float
    {
        return $this->medianOdd;
    }

    public function setMedianOdd(float $medianOdd): BetAccumulation
    {
        $this->medianOdd = $medianOdd;
        return $this;
    }
}
