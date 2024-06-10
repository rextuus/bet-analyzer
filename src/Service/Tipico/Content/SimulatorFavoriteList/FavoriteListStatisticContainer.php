<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;


class FavoriteListStatisticContainer
{
    private float $currentBalance = 0.0;
    private int $possiblePlacements = 0;
    private int $donePlacements = 0;

    /**
     * @var FavoriteListStatisticSimulatorContainer[]
     */
    private array $simulatorDetails = [];

    private string $listClass = 'negative';

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(float $currentBalance): FavoriteListStatisticContainer
    {
        $this->currentBalance = $currentBalance;
        return $this;
    }

    public function getPossiblePlacements(): int
    {
        return $this->possiblePlacements;
    }

    public function setPossiblePlacements(int $possiblePlacements): FavoriteListStatisticContainer
    {
        $this->possiblePlacements = $possiblePlacements;
        return $this;
    }

    public function getDonePlacements(): int
    {
        return $this->donePlacements;
    }

    public function setDonePlacements(int $donePlacements): FavoriteListStatisticContainer
    {
        $this->donePlacements = $donePlacements;
        return $this;
    }

    public function getSimulatorDetails(): array
    {
        return $this->simulatorDetails;
    }

    public function setSimulatorDetails(array $simulatorDetails): FavoriteListStatisticContainer
    {
        $this->simulatorDetails = $simulatorDetails;
        return $this;
    }

    public function getListClass(): string
    {
        return $this->listClass;
    }

    public function setListClass(string $listClass): FavoriteListStatisticContainer
    {
        $this->listClass = $listClass;
        return $this;
    }
}
