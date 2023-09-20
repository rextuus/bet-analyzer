<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\SpmSeason;
use App\Service\Evaluation\OddAccumulationVariant;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class InitSimpleBetRowsForSeasonData
{
    private SpmSeason $season;
    private float $max;
    private float $min;
    private float $steps;
    private float $initialCashBox;
    private float $wager;
    private bool $includeTax = true;
    private OddAccumulationVariant $oddAccumulationVariant;

    public function getSeason(): SpmSeason
    {
        return $this->season;
    }

    public function setSeason(SpmSeason $season): InitSimpleBetRowsForSeasonData
    {
        $this->season = $season;
        return $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): InitSimpleBetRowsForSeasonData
    {
        $this->max = $max;
        return $this;
    }

    public function getSteps(): float
    {
        return $this->steps;
    }

    public function setSteps(float $steps): InitSimpleBetRowsForSeasonData
    {
        $this->steps = $steps;
        return $this;
    }

    public function isIncludeTax(): bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): InitSimpleBetRowsForSeasonData
    {
        $this->includeTax = $includeTax;
        return $this;
    }

    public function getOddAccumulationVariant(): OddAccumulationVariant
    {
        return $this->oddAccumulationVariant;
    }

    public function setOddAccumulationVariant(OddAccumulationVariant $oddAccumulationVariant): InitSimpleBetRowsForSeasonData
    {
        $this->oddAccumulationVariant = $oddAccumulationVariant;
        return $this;
    }

    public function getInitialCashBox(): float
    {
        return $this->initialCashBox;
    }

    public function setInitialCashBox(float $initialCashBox): InitSimpleBetRowsForSeasonData
    {
        $this->initialCashBox = $initialCashBox;
        return $this;
    }

    public function getWager(): float
    {
        return $this->wager;
    }

    public function setWager(float $wager): InitSimpleBetRowsForSeasonData
    {
        $this->wager = $wager;
        return $this;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): InitSimpleBetRowsForSeasonData
    {
        $this->min = $min;
        return $this;
    }
}
