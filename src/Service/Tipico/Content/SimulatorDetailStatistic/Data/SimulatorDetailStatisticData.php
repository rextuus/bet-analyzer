<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorDetailStatistic\Data;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorDetailStatistic;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorDetailStatisticData
{
    private Simulator $simulator;

    private float $mondayTotal = 0.0;

    private float $mondayAverage = 0.0;

    private float $tuesdayTotal = 0.0;

    private float $tuesdayAverage = 0.0;

    private float $wednesdayTotal = 0.0;

    private float $wednesdayAverage = 0.0;

    private float $thursdayTotal = 0.0;

    private float $thursdayAverage = 0.0;

    private float $fridayTotal = 0.0;

    private float $fridayAverage = 0.0;

    private float $saturdayTotal = 0.0;

    private float $saturdayAverage = 0.0;

    private float $sundayTotal = 0.0;

    private float $sundayAverage = 0.0;

    public function getSimulator(): Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(Simulator $simulator): SimulatorDetailStatisticData
    {
        $this->simulator = $simulator;
        return $this;
    }

    public function getMondayTotal(): float
    {
        return $this->mondayTotal;
    }

    public function setMondayTotal(float $mondayTotal): SimulatorDetailStatisticData
    {
        $this->mondayTotal = $mondayTotal;
        return $this;
    }

    public function getMondayAverage(): float
    {
        return $this->mondayAverage;
    }

    public function setMondayAverage(float $mondayAverage): SimulatorDetailStatisticData
    {
        $this->mondayAverage = $mondayAverage;
        return $this;
    }

    public function getTuesdayTotal(): float
    {
        return $this->tuesdayTotal;
    }

    public function setTuesdayTotal(float $tuesdayTotal): SimulatorDetailStatisticData
    {
        $this->tuesdayTotal = $tuesdayTotal;
        return $this;
    }

    public function getTuesdayAverage(): float
    {
        return $this->tuesdayAverage;
    }

    public function setTuesdayAverage(float $tuesdayAverage): SimulatorDetailStatisticData
    {
        $this->tuesdayAverage = $tuesdayAverage;
        return $this;
    }

    public function getWednesdayTotal(): float
    {
        return $this->wednesdayTotal;
    }

    public function setWednesdayTotal(float $wednesdayTotal): SimulatorDetailStatisticData
    {
        $this->wednesdayTotal = $wednesdayTotal;
        return $this;
    }

    public function getWednesdayAverage(): float
    {
        return $this->wednesdayAverage;
    }

    public function setWednesdayAverage(float $wednesdayAverage): SimulatorDetailStatisticData
    {
        $this->wednesdayAverage = $wednesdayAverage;
        return $this;
    }

    public function getThursdayTotal(): float
    {
        return $this->thursdayTotal;
    }

    public function setThursdayTotal(float $thursdayTotal): SimulatorDetailStatisticData
    {
        $this->thursdayTotal = $thursdayTotal;
        return $this;
    }

    public function getThursdayAverage(): float
    {
        return $this->thursdayAverage;
    }

    public function setThursdayAverage(float $thursdayAverage): SimulatorDetailStatisticData
    {
        $this->thursdayAverage = $thursdayAverage;
        return $this;
    }

    public function getFridayTotal(): float
    {
        return $this->fridayTotal;
    }

    public function setFridayTotal(float $fridayTotal): SimulatorDetailStatisticData
    {
        $this->fridayTotal = $fridayTotal;
        return $this;
    }

    public function getFridayAverage(): float
    {
        return $this->fridayAverage;
    }

    public function setFridayAverage(float $fridayAverage): SimulatorDetailStatisticData
    {
        $this->fridayAverage = $fridayAverage;
        return $this;
    }

    public function getSaturdayTotal(): float
    {
        return $this->saturdayTotal;
    }

    public function setSaturdayTotal(float $saturdayTotal): SimulatorDetailStatisticData
    {
        $this->saturdayTotal = $saturdayTotal;
        return $this;
    }

    public function getSaturdayAverage(): float
    {
        return $this->saturdayAverage;
    }

    public function setSaturdayAverage(float $saturdayAverage): SimulatorDetailStatisticData
    {
        $this->saturdayAverage = $saturdayAverage;
        return $this;
    }

    public function getSundayTotal(): float
    {
        return $this->sundayTotal;
    }

    public function setSundayTotal(float $sundayTotal): SimulatorDetailStatisticData
    {
        $this->sundayTotal = $sundayTotal;
        return $this;
    }

    public function getSundayAverage(): float
    {
        return $this->sundayAverage;
    }

    public function setSundayAverage(float $sundayAverage): SimulatorDetailStatisticData
    {
        $this->sundayAverage = $sundayAverage;
        return $this;
    }

    public function initFromEntity(SimulatorDetailStatistic $simulatorDetailStatistic): SimulatorDetailStatisticData
    {
        $this->setSimulator($simulatorDetailStatistic->getSimulator());
        $this->setMondayTotal($simulatorDetailStatistic->getMondayTotal());
        $this->setMondayAverage($simulatorDetailStatistic->getMondayAverage());
        $this->setTuesdayTotal($simulatorDetailStatistic->getTuesdayTotal());
        $this->setTuesdayAverage($simulatorDetailStatistic->getTuesdayAverage());
        $this->setWednesdayTotal($simulatorDetailStatistic->getWednesdayTotal());
        $this->setWednesdayAverage($simulatorDetailStatistic->getWednesdayAverage());
        $this->setThursdayTotal($simulatorDetailStatistic->getThursdayTotal());
        $this->setThursdayAverage($simulatorDetailStatistic->getThursdayAverage());
        $this->setFridayTotal($simulatorDetailStatistic->getFridayTotal());
        $this->setFridayAverage($simulatorDetailStatistic->getFridayAverage());
        $this->setSaturdayTotal($simulatorDetailStatistic->getSaturdayTotal());
        $this->setSaturdayAverage($simulatorDetailStatistic->getSaturdayAverage());
        $this->setSundayTotal($simulatorDetailStatistic->getSundayTotal());
        $this->setSundayAverage($simulatorDetailStatistic->getSundayAverage());

        return $this;
    }
}
