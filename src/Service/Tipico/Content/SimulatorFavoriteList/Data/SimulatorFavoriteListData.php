<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use DateTimeInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorFavoriteListData
{
    private string $identifier;
    private DateTimeInterface $created;
    private float $totalCashBox = 0.0;
    private int $bets = 0;

    /**
     * @var Simulator[]
     */
    private array $simulators = [];

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): SimulatorFavoriteListData
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): SimulatorFavoriteListData
    {
        $this->created = $created;
        return $this;
    }

    public function getTotalCashBox(): float
    {
        return $this->totalCashBox;
    }

    public function setTotalCashBox(float $totalCashBox): SimulatorFavoriteListData
    {
        $this->totalCashBox = $totalCashBox;
        return $this;
    }

    public function getSimulators(): array
    {
        return $this->simulators;
    }

    public function setSimulators(array $simulators): SimulatorFavoriteListData
    {
        $this->simulators = $simulators;
        return $this;
    }

    public function getBets(): int
    {
        return $this->bets;
    }

    public function setBets(int $bets): SimulatorFavoriteListData
    {
        $this->bets = $bets;
        return $this;
    }



    public function initFromEntity(SimulatorFavoriteList $favoriteList): SimulatorFavoriteListData
    {
        $this->setIdentifier($favoriteList->getIdentifier());
        $this->setCreated($favoriteList->getCreated());
        $this->setTotalCashBox($favoriteList->getTotalCashBox());
        $this->setBets($favoriteList->getBets());

        return $this;
    }
}
