<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Entity\TipicoPlacement;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OutcomeTestCalculation\TestBet;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBetSimulator
{
    /**
     * @param TipicoBet[] $fixtures
     */
    public function combineFixtures(Simulator $simulator, array $fixtures, BetOn $betOn): TipicoPlacementData
    {
        $combinationIsWon = true;
        foreach ($fixtures as $bet){
            if ($bet->getResult() != $betOn){
                $combinationIsWon = false;
            }
        }

        $combinationValue = 0.0;
        if ($combinationIsWon){
            $combinationValue = 1.0;
            foreach ($fixtures as $bet){
                match($betOn){
                    BetOn::HOME => $value = $bet->getOddHome(),
                    BetOn::DRAW => $value = $bet->getOddDraw(),
                    BetOn::AWAY => $value = $bet->getOddAway(),
                };
                $combinationValue = $combinationValue * $value;
            }

        }

        $data = new TipicoPlacementData();
        $data->setFixtures($fixtures);
        $data->setInput(1.0);
        $data->setValue($combinationValue);
        $data->setCreated(new DateTime());
        $data->setWon($combinationIsWon);
        $data->setSimulator($simulator);

        return $data;
    }
}
