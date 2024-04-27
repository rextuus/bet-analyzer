<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement\Data;

use App\Entity\BettingProvider\Simulator;
use DateTimeInterface;


class TipicoPlacementData
{
    private array $fixtures;
    private float $value;
    private Simulator $simulator;
    private DateTimeInterface $created;
    private bool $won;
    private float $input;

    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    public function setFixtures(array $fixtures): TipicoPlacementData
    {
        $this->fixtures = $fixtures;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): TipicoPlacementData
    {
        $this->value = $value;
        return $this;
    }

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): TipicoPlacementData
    {
        $this->simulator = $simulator;
        return $this;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): TipicoPlacementData
    {
        $this->created = $created;
        return $this;
    }

    public function isWon(): bool
    {
        return $this->won;
    }

    public function setWon(bool $won): TipicoPlacementData
    {
        $this->won = $won;
        return $this;
    }

    public function getInput(): float
    {
        return $this->input;
    }

    public function setInput(float $input): TipicoPlacementData
    {
        $this->input = $input;
        return $this;
    }
}
