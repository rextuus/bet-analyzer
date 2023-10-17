<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowCombination\Data;

use App\Entity\BetRowCombination;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCombinationFactory
{
    public function createByData(BetRowCombinationData $data): BetRowCombination
    {
        $betRowCombination = $this->createNewInstance();
        $this->mapData($data, $betRowCombination);
        return $betRowCombination;
    }

    public function mapData(BetRowCombinationData $data, BetRowCombination $betRowCombination): BetRowCombination
    {
        $betRowCombination->setIdent($data->getIdent());
        $betRowCombination->setActive($data->isActive());
        $betRowCombination->setEvaluated($data->isEvaluated());

        foreach ($data->getRows() as $row){
            $betRowCombination->addBetRow($row);
        }

        return $betRowCombination;
    }

    private function createNewInstance(): BetRowCombination
    {
        return new BetRowCombination();
    }
}
