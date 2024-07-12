<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic\Data;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorDetailStatistic;
use DateTimeInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorDetailStatisticData
{
    private DateTimeInterface $created;

    private Simulator $simulator;

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): SimulatorDetailStatisticData
    {
        $this->created = $created;
        return $this;
    }

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): SimulatorDetailStatisticData
    {
        $this->simulator = $simulator;
        return $this;
    }

    public function initFromEntity(SimulatorDetailStatistic $simulatorDetailStatistic): SimulatorDetailStatisticData
    {
        $this->setCreated($simulatorDetailStatistic->getCreationDate());
        $this->setSimulator($simulatorDetailStatistic->getSimulator());

        return $this;
    }
}
