<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Simulator\Data;

use App\Entity\BettingProvider\SimulationStrategy;
use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoBet;
use App\Entity\BettingProvider\TipicoPlacement;


class SimulatorData
{
    private float $cashBox;

    /**
     * @var TipicoBet[] $fixtures
     */
    private array $fixtures;

    private string $identifier;
    private SimulationStrategy $strategy;

    private float $currentIn;

    /**
     * @var TipicoPlacement[] $placements
     */
    private array $placements;

    public function getCashBox(): float
    {
        return $this->cashBox;
    }

    public function setCashBox(float $cashBox): SimulatorData
    {
        $this->cashBox = $cashBox;
        return $this;
    }

    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    public function setFixtures(array $fixtures): SimulatorData
    {
        $this->fixtures = $fixtures;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): SimulatorData
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getStrategy(): SimulationStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(SimulationStrategy $strategy): SimulatorData
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function getPlacements(): array
    {
        return $this->placements;
    }

    public function setPlacements(array $placements): SimulatorData
    {
        $this->placements = $placements;
        return $this;
    }

    public function getCurrentIn(): float
    {
        return $this->currentIn;
    }

    public function setCurrentIn(float $currentIn): SimulatorData
    {
        $this->currentIn = $currentIn;
        return $this;
    }

    public function initFromEntity(Simulator $simulator): SimulatorData
    {
        $this->setFixtures([]);
        $this->setPlacements([]);
        $this->setStrategy($simulator->getStrategy());
        $this->setIdentifier($simulator->getIdentifier());
        $this->setCashBox($simulator->getCashBox());
        $this->setCurrentIn($simulator->getCurrentIn());

        return $this;
    }
}
