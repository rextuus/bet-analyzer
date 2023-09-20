<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRowOddFilter;

use App\Entity\BetRowOddFilter;
use App\Service\Evaluation\Content\BetRowOddFilter\Data\BetRowOddFilterData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowOddFilterFactory
{
    public function createByData(BetRowOddFilterData $data): BetRowOddFilter
    {
        $betRowOddFilter = $this->createNewInstance();
        $this->mapData($data, $betRowOddFilter);
        return $betRowOddFilter;
    }

    public function mapData(BetRowOddFilterData $data, BetRowOddFilter $betRowOddFilter): BetRowOddFilter
    {
        $betRowOddFilter->setBetOn($data->getBetOn());
        $betRowOddFilter->setOddVariant($data->getOddVariant());
        $betRowOddFilter->setMin($data->getMin());
        $betRowOddFilter->setMax($data->getMax());

        return $betRowOddFilter;
    }

    private function createNewInstance(): BetRowOddFilter
    {
        return new BetRowOddFilter();
    }
}
