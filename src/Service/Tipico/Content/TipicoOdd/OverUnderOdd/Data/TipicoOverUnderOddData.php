<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\Data;

use App\Entity\BettingProvider\TipicoBet;


class TipicoOverUnderOddData
{
    private TipicoBet $bet;
    private int $tipicoBetId;
    private float $over;
    private float $under;
    private float $target;

    public function getBet(): TipicoBet
    {
        return $this->bet;
    }

    public function setBet(TipicoBet $bet): TipicoOverUnderOddData
    {
        $this->bet = $bet;
        return $this;
    }

    public function getOver(): float
    {
        return $this->over;
    }

    public function setOver(float $over): TipicoOverUnderOddData
    {
        $this->over = $over;
        return $this;
    }

    public function getTarget(): float
    {
        return $this->target;
    }

    public function setTarget(float $target): TipicoOverUnderOddData
    {
        $this->target = $target;
        return $this;
    }

    public function getUnder(): float
    {
        return $this->under;
    }

    public function setUnder(float $under): TipicoOverUnderOddData
    {
        $this->under = $under;
        return $this;
    }

    public function getTipicoBetId(): int
    {
        return $this->tipicoBetId;
    }

    public function setTipicoBetId(int $tipicoBetId): TipicoOverUnderOddData
    {
        $this->tipicoBetId = $tipicoBetId;
        return $this;
    }

    public function initFromApiResponse(array $data, int $betId): TipicoOverUnderOddData
    {
        $this->setOver($data['overValue']);
        $this->setUnder($data['underValue']);
        $this->setTarget($data['targetValue']);
        $this->setTipicoBetId($betId);

        return $this;
    }
}
