<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\PlacedBet;

use App\Entity\Spm\PlacedBet;
use App\Service\Evaluation\Content\PlacedBet\Data\PlacedBetData;


class PlacedBetFactory
{
    public function createByData(PlacedBetData $data): PlacedBet
    {
        $placedBet = $this->createNewInstance();
        $this->mapData($data, $placedBet);
        return $placedBet;
    }

    public function mapData(PlacedBetData $data, PlacedBet $placedBet): PlacedBet
    {
        $placedBet->setWager($data->getWager());
        $placedBet->setBetRow($data->getBetRow());
        $placedBet->setVariant($data->getVariant());
        $placedBet->setOddApiIds($data->getOddApiIds());
        $placedBet->setFixtureApiId($data->getFixtureApiId());
        $placedBet->setOdd($data->getOdd());
        $placedBet->setMatchDay($data->getMatchDay());
        $placedBet->setWon($data->isWon());
        $placedBet->setOutput($data->getOutput());

        return $placedBet;
    }

    private function createNewInstance(): PlacedBet
    {
        return new PlacedBet();
    }
}
