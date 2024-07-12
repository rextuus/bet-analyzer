<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic\PlacementDistribution;

use App\Entity\BettingProvider\TipicoPlacement;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BasePlacementDistribution
{
    /**
     * @var array<TipicoPlacement>
     */
    private array $placements;

    public function getPlacements(): array
    {
        return $this->placements;
    }

    public function setPlacements(array $placements): BasePlacementDistribution
    {
        $this->placements = $placements;

        return $this;
    }
}
