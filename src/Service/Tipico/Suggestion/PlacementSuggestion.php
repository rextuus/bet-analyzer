<?php

declare(strict_types=1);

namespace App\Service\Tipico\Suggestion;

use App\Entity\BettingProvider\Simulator;
use App\Service\Evaluation\BetOn;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class PlacementSuggestion
{
    private BetOn $targetBetOn;

    private string $simulatorIdent;
    private int $simulatorId;

    private Simulator $simulator;

    private ?float $targetValue = null;

    public function getTargetBetOn(): BetOn
    {
        return $this->targetBetOn;
    }

    public function setTargetBetOn(BetOn $targetBetOn): PlacementSuggestion
    {
        $this->targetBetOn = $targetBetOn;
        return $this;
    }

    public function getTargetValue(): ?float
    {
        return $this->targetValue;
    }

    public function setTargetValue(?float $targetValue): PlacementSuggestion
    {
        $this->targetValue = $targetValue;
        return $this;
    }

    public function getSimulatorIdent(): string
    {
        return $this->simulatorIdent;
    }

    public function setSimulatorIdent(string $simulatorIdent): PlacementSuggestion
    {
        $this->simulatorIdent = $simulatorIdent;
        return $this;
    }

    public function getSimulatorId(): int
    {
        return $this->simulatorId;
    }

    public function setSimulatorId(int $simulatorId): PlacementSuggestion
    {
        $this->simulatorId = $simulatorId;
        return $this;
    }

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): PlacementSuggestion
    {
        $this->simulator = $simulator;
        return $this;
    }
}
