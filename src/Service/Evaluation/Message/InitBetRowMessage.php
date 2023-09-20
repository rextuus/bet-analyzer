<?php

namespace App\Service\Evaluation\Message;

use App\Entity\BetRowOddFilter;
use App\Form\InitSimpleBetRowsForSeasonData;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;

final class InitBetRowMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private InitSimpleBetRowsForSeasonData $data;
     private BetOn $betOnVariant;
     private float $from;
     private float $to;

    public function __construct(InitSimpleBetRowsForSeasonData $data, BetOn $betOnVariant, float $from, float $to)
    {
        $this->data = $data;
        $this->betOnVariant = $betOnVariant;
        $this->from = $from;
        $this->to = $to;
    }

    public function getData(): InitSimpleBetRowsForSeasonData
    {
        return $this->data;
    }

    public function setData(InitSimpleBetRowsForSeasonData $data): InitBetRowMessage
    {
        $this->data = $data;
        return $this;
    }

    public function getBetOnVariant(): BetOn
    {
        return $this->betOnVariant;
    }

    public function setBetOnVariant(BetOn $betOnVariant): InitBetRowMessage
    {
        $this->betOnVariant = $betOnVariant;
        return $this;
    }

    public function getFrom(): float
    {
        return $this->from;
    }

    public function setFrom(float $from): InitBetRowMessage
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): float
    {
        return $this->to;
    }

    public function setTo(float $to): InitBetRowMessage
    {
        $this->to = $to;
        return $this;
    }
}
