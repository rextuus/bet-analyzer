<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\Data;

use App\Entity\TipicoBet;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoHeadToHeadOddData
{
    private TipicoBet $bet;
    private int $tipicoBetId;
    private float $homeTeamValue;
    private float $awayTeamValue;

    public function getBet(): TipicoBet
    {
        return $this->bet;
    }

    public function setBet(TipicoBet $bet): TipicoHeadToHeadOddData
    {
        $this->bet = $bet;
        return $this;
    }

    public function getTipicoBetId(): int
    {
        return $this->tipicoBetId;
    }

    public function setTipicoBetId(int $tipicoBetId): TipicoHeadToHeadOddData
    {
        $this->tipicoBetId = $tipicoBetId;
        return $this;
    }

    public function getHomeTeamValue(): float
    {
        return $this->homeTeamValue;
    }

    public function setHomeTeamValue(float $homeTeamValue): TipicoHeadToHeadOddData
    {
        $this->homeTeamValue = $homeTeamValue;
        return $this;
    }

    public function getAwayTeamValue(): float
    {
        return $this->awayTeamValue;
    }

    public function setAwayTeamValue(float $awayTeamValue): TipicoHeadToHeadOddData
    {
        $this->awayTeamValue = $awayTeamValue;
        return $this;
    }

    public function initFromApiResponse(array $data, int $tipicoId): TipicoHeadToHeadOddData
    {
        $this->setTipicoBetId($tipicoId);
        $this->setHomeTeamValue($data['homeTeamValue']);
        $this->setAwayTeamValue($data['awayTeamValue']);

        return $this;
    }
}
