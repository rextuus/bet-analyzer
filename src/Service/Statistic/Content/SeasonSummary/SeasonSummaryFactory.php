<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\SeasonSummary;

use App\Entity\Spm\SeasonSummary;
use App\Service\Statistic\Content\SeasonSummary\Data\SeasonSummaryData;


class SeasonSummaryFactory
{
    public function createByData(SeasonSummaryData $data): SeasonSummary
    {
        $seasonSummary = $this->createNewInstance();
        $this->mapData($data, $seasonSummary);
        return $seasonSummary;
    }

    public function mapData(SeasonSummaryData $data, SeasonSummary $seasonSummary): SeasonSummary
    {
        $seasonSummary->setSeason($data->getSeason());
        $seasonSummary->setHighest($data->getHighest());
        $seasonSummary->setMissingAwayFilters($data->getMissingAwayFilters());
        $seasonSummary->setMissingDrawFilters($data->getMissingDrawFilters());
        $seasonSummary->setMissingHomeFilters($data->getMissingHomeFilters());

        return $seasonSummary;
    }

    private function createNewInstance(): SeasonSummary
    {
        return new SeasonSummary();
    }
}
