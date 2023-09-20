<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRow\SimpleBetRow;

use App\Entity\SimpleBetRow;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data\SimpleBetRowData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SimpleBetRowFactory
{
    public function createByData(SimpleBetRowData $data): SimpleBetRow
    {
        $simpleBetRow = $this->createNewInstance();
        $this->mapData($data, $simpleBetRow);
        return $simpleBetRow;
    }

    public function mapData(SimpleBetRowData $data, SimpleBetRow $simpleBetRow): SimpleBetRow
    {
        $simpleBetRow->setVariant($data->getVariant());
        $simpleBetRow->setSeasonApiId($data->getSeasonApiId());
        $simpleBetRow->setCashBox($data->getCashBox());
        $simpleBetRow->setLeagueApiId($data->getLeagueApiId());
        $simpleBetRow->setWager($data->getWager());
        $simpleBetRow->setAccumulationVariant($data->getOddAccumulationVariant());
        $simpleBetRow->setIncludeTax($data->isIncludeTaxes());

        foreach ($data->getFilters() as $filter){
            $simpleBetRow->addBetRowFilter($filter);
        }

        return $simpleBetRow;
    }

    private function createNewInstance(): SimpleBetRow
    {
        return new SimpleBetRow();
    }
}
