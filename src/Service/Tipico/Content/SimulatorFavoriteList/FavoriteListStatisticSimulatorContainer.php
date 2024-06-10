<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;


use App\Entity\BettingProvider\Simulator;

class FavoriteListStatisticSimulatorContainer
{
    private float $currentBalance = 0.0;
    private int $possiblePlacements = 0;
    private int $donePlacements = 0;

    private string $simulatorClass = '';

    private Simulator $simulator;

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(float $currentBalance): FavoriteListStatisticSimulatorContainer
    {
        $this->currentBalance = $currentBalance;
        return $this;
    }

    public function getPossiblePlacements(): int
    {
        return $this->possiblePlacements;
    }

    public function setPossiblePlacements(int $possiblePlacements): FavoriteListStatisticSimulatorContainer
    {
        $this->possiblePlacements = $possiblePlacements;
        return $this;
    }

    public function getDonePlacements(): int
    {
        return $this->donePlacements;
    }

    public function setDonePlacements(int $donePlacements): FavoriteListStatisticSimulatorContainer
    {
        $this->donePlacements = $donePlacements;
        return $this;
    }

    public function getSimulatorClass(): string
    {
        return $this->simulatorClass;
    }

    public function setSimulatorClass(string $simulatorClass): FavoriteListStatisticSimulatorContainer
    {
        $this->simulatorClass = $simulatorClass;
        return $this;
    }

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): FavoriteListStatisticSimulatorContainer
    {
        $this->simulator = $simulator;
        return $this;
    }
}
