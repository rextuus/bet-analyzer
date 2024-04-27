<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd;

use App\Entity\BettingProvider\TipicoBothTeamsScoreOdd;
use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\Data\TipicoBothTeamsScoreOddData;


class TipicoBothTeamsScoreOddFactory
{
    public function createByData(TipicoBothTeamsScoreOddData $data): TipicoBothTeamsScoreOdd
    {
        $tipicoBothTeamsScoreOdd = $this->createNewInstance();
        $this->mapData($data, $tipicoBothTeamsScoreOdd);
        return $tipicoBothTeamsScoreOdd;
    }

    public function mapData(TipicoBothTeamsScoreOddData $data, TipicoBothTeamsScoreOdd $tipicoBothTeamsScoreOdd): TipicoBothTeamsScoreOdd
    {
        $tipicoBothTeamsScoreOdd->setBet($data->getBet());
        $tipicoBothTeamsScoreOdd->setConditionFalseValue($data->getConditionFalseValue());
        $tipicoBothTeamsScoreOdd->setConditionTrueValue($data->getConditionTrueValue());

        return $tipicoBothTeamsScoreOdd;
    }

    private function createNewInstance(): TipicoBothTeamsScoreOdd
    {
        return new TipicoBothTeamsScoreOdd();
    }
}
