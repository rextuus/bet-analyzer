<?php
declare(strict_types=1);

namespace App\Service\Tipico\Simulation\Data;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;


class ProcessResult
{
    /**
     * @var TipicoPlacementData[]
     */
    private array $placementData = [];

    /**
     * @var TipicoBet[]
     */
    private array $fixturesActuallyUsed = [];

    public function getPlacementData(): array
    {
        return $this->placementData;
    }

    public function setPlacementData(array $placementData): ProcessResult
    {
        $this->placementData = $placementData;
        return $this;
    }

    public function getFixturesActuallyUsed(): array
    {
        return $this->fixturesActuallyUsed;
    }

    public function setFixturesActuallyUsed(array $fixturesActuallyUsed): ProcessResult
    {
        $this->fixturesActuallyUsed = $fixturesActuallyUsed;
        return $this;
    }
}
