<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class AddSimulatorToListData
{
    private Simulator $simulator;

    private SimulatorFavoriteList $simulatorFavoriteList;

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): AddSimulatorToListData
    {
        $this->simulator = $simulator;
        return $this;
    }

    public function getSimulatorFavoriteList(): SimulatorFavoriteList
    {
        return $this->simulatorFavoriteList;
    }

    public function setSimulatorFavoriteList(SimulatorFavoriteList $simulatorFavoriteList): AddSimulatorToListData
    {
        $this->simulatorFavoriteList = $simulatorFavoriteList;
        return $this;
    }
}
