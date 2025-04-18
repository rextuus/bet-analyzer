<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\Spm\BetRowSummary;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class SeasonBetRowMap
{
    private array $map;

    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function addSeason(int $seasonApiId): void
    {
        $this->map[$seasonApiId] = [];
    }

    public function addBetRow(int $seasonApiId, BetRowSummary $betRow): void
    {
        if (!array_key_exists($seasonApiId, $this->map)) {
            throw new \Exception($seasonApiId . ' did not exist');
        }
        $filter = $betRow->getBetRow()->getBetRowFilters()->toArray()[0];
        $key = $filter->getMin().'-'.$filter->getMax().'-'.$filter->getBetOn()->value;
        $this->map[$seasonApiId][$key] = $betRow;
    }

    public function getMap(): array
    {
        return ($this->map);
    }
}
