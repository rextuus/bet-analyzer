<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\Data;

use App\Entity\TipicoBet;
use App\Service\Tipico\Content\TipicoOdd\TipicoOddVariant;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
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

}
