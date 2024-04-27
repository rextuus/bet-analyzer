<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowSummary;

use App\Entity\Spm\BetRowSummary;
use App\Service\Statistic\Content\BetRowSummary\Data\BetRowSummaryData;


class BetRowSummaryFactory
{
    public function createByData(BetRowSummaryData $data): BetRowSummary
    {
        $betRowSummary = $this->createNewInstance();
        $this->mapData($data, $betRowSummary);
        return $betRowSummary;
    }

    public function mapData(BetRowSummaryData $data, BetRowSummary $betRowSummary): BetRowSummary
    {
        $betRowSummary->setBetRow($data->getBetRow());
        $betRowSummary->setMadeBets($data->getMadeBets());
        $betRowSummary->setHighest($data->getHighest());
        $betRowSummary->setLowest($data->getLowest());
        $betRowSummary->setCashBox($data->getCashBox());
        $betRowSummary->setDailyReproductionChance($data->getDailyReproductionChance());
        $betRowSummary->setSeriesStatistics($data->getSeriesStatistics());
        $betRowSummary->setDaysMadeBets($data->getDaysMadeBets());
        $betRowSummary->setDaysOutcomes($data->getDaysOutcomes());
        $betRowSummary->setPositiveDays($data->getPositiveDays());
dump($betRowSummary->getSeriesStatistics());
        return $betRowSummary;
    }

    private function createNewInstance(): BetRowSummary
    {
        return new BetRowSummary();
    }
}
