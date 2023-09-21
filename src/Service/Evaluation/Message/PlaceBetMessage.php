<?php

namespace App\Service\Evaluation\Message;

use App\Entity\BetRowOddFilter;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;

final class PlaceBetMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private OddAccumulationVariant $accumulationVariant;
     private int $fixtureApiId;
     private int $betRowOddFilterId;

     private int $betRowId;
     private BetRowVariant $betRowVariant;
     private float $wager;
     private bool $includeTax;

    public function __construct(OddAccumulationVariant $accumulationVariant, int $fixtureApiId, int $betRowOddFilterId, int $betRowId, BetRowVariant $betRowVariant, float $wager, bool $includeTax)
    {
        $this->accumulationVariant = $accumulationVariant;
        $this->fixtureApiId = $fixtureApiId;
        $this->betRowOddFilterId = $betRowOddFilterId;
        $this->betRowId = $betRowId;
        $this->betRowVariant = $betRowVariant;
        $this->wager = $wager;
        $this->includeTax = $includeTax;
    }


    public function getAccumulationVariant(): OddAccumulationVariant
    {
        return $this->accumulationVariant;
    }

    public function setAccumulationVariant(OddAccumulationVariant $accumulationVariant): PlaceBetMessage
    {
        $this->accumulationVariant = $accumulationVariant;
        return $this;
    }

    public function getFixtureApiId(): int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): PlaceBetMessage
    {
        $this->fixtureApiId = $fixtureApiId;
        return $this;
    }

    public function getBetRowOddFilterId(): int
    {
        return $this->betRowOddFilterId;
    }

    public function setBetRowOddFilterId(int $betRowOddFilterId): PlaceBetMessage
    {
        $this->betRowOddFilterId = $betRowOddFilterId;
        return $this;
    }

    public function getBetRowId(): int
    {
        return $this->betRowId;
    }

    public function setBetRowId(int $betRowId): PlaceBetMessage
    {
        $this->betRowId = $betRowId;
        return $this;
    }

    public function getBetRowVariant(): BetRowVariant
    {
        return $this->betRowVariant;
    }

    public function setBetRowVariant(BetRowVariant $betRowVariant): PlaceBetMessage
    {
        $this->betRowVariant = $betRowVariant;
        return $this;
    }

    public function getWager(): float
    {
        return $this->wager;
    }

    public function setWager(float $wager): PlaceBetMessage
    {
        $this->wager = $wager;
        return $this;
    }

    public function isIncludeTax(): bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): PlaceBetMessage
    {
        $this->includeTax = $includeTax;
        return $this;
    }
}
