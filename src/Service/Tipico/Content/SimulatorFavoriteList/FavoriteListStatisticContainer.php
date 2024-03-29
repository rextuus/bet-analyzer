<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class FavoriteListStatisticContainer
{
    private float $currentBalance = 0.0;
    private int $possiblePlacements = 0;
    private int $donePlacements = 0;

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
}
