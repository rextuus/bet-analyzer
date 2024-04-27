<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorFavoriteList;


class AddSimulatorToListData
{
    private ?Simulator $simulator = null;

    private ?SimulatorFavoriteList $simulatorFavoriteList = null;

//    /**
//     * @param Simulator|null $simulator
//     */
//    public function __construct(?Simulator $simulator)
//    {
//        $this->simulator = $simulator;
//    }

    public function getSimulator(): ?Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(?Simulator $simulator): AddSimulatorToListData
    {
        $this->simulator = $simulator;
        return $this;
    }

    public function getSimulatorFavoriteList(): ?SimulatorFavoriteList
    {
        return $this->simulatorFavoriteList;
    }

    public function setSimulatorFavoriteList(?SimulatorFavoriteList $simulatorFavoriteList): AddSimulatorToListData
    {
        $this->simulatorFavoriteList = $simulatorFavoriteList;
        return $this;
    }
}
