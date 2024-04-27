<?php
declare(strict_types=1);
namespace App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\Data;

use App\Entity\BettingProvider\TipicoBet;


class TipicoBothTeamsScoreOddData
{
    private TipicoBet $bet;
    private int $tipicoBetId;
    private float $conditionTrueValue;
    private float $conditionFalseValue;

    public function getBet(): TipicoBet
    {
        return $this->bet;
    }

    public function setBet(TipicoBet $bet): TipicoBothTeamsScoreOddData
    {
        $this->bet = $bet;
        return $this;
    }

    public function getConditionTrueValue(): float
    {
        return $this->conditionTrueValue;
    }

    public function setConditionTrueValue(float $conditionTrueValue): TipicoBothTeamsScoreOddData
    {
        $this->conditionTrueValue = $conditionTrueValue;
        return $this;
    }

    public function getConditionFalseValue(): float
    {
        return $this->conditionFalseValue;
    }

    public function setConditionFalseValue(float $conditionFalseValue): TipicoBothTeamsScoreOddData
    {
        $this->conditionFalseValue = $conditionFalseValue;
        return $this;
    }

    public function getTipicoBetId(): int
    {
        return $this->tipicoBetId;
    }

    public function setTipicoBetId(int $tipicoBetId): TipicoBothTeamsScoreOddData
    {
        $this->tipicoBetId = $tipicoBetId;
        return $this;
    }

    public function initFromApiResponse(array $data, int $tipicoId): TipicoBothTeamsScoreOddData
    {
        $this->setTipicoBetId($tipicoId);
        $this->setConditionTrueValue($data['conditionTrueValue']);
        $this->setConditionFalseValue($data['conditionFalseValue']);

        return $this;
    }

}
