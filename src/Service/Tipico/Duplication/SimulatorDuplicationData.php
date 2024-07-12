<?php

declare(strict_types=1);

namespace App\Service\Tipico\Duplication;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorDuplicationData
{
    private bool $initProcessing = true;

    private ?array $weekdays = null;

    public function isInitProcessing(): bool
    {
        return $this->initProcessing;
    }

    public function setInitProcessing(bool $initProcessing): SimulatorDuplicationData
    {
        $this->initProcessing = $initProcessing;
        return $this;
    }

    public function getWeekdays(): ?array
    {
        return $this->weekdays;
    }

    public function setWeekdays(?array $weekdays): SimulatorDuplicationData
    {
        $this->weekdays = $weekdays;
        return $this;
    }
}
