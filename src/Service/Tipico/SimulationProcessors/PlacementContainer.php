<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\TipicoPlacement;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class PlacementContainer
{
    /**
     * @var TipicoPlacement[]
     */
    private array $placements;

    private float $cashBoxChange;

    public function getPlacements(): array
    {
        return $this->placements;
    }

    public function setPlacements(array $placements): PlacementContainer
    {
        $this->placements = $placements;
        return $this;
    }

    public function getCashBoxChange(): float
    {
        return $this->cashBoxChange;
    }

    public function setCashBoxChange(float $cashBoxChange): PlacementContainer
    {
        $this->cashBoxChange = $cashBoxChange;
        return $this;
    }
}
