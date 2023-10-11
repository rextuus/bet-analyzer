<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowSummary\Data;

use App\Entity\SimpleBetRow;
use App\Service\Statistic\SeriesStatistic;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowSummaryData
{
    private ?SimpleBetRow $betRow;

    private ?float $cashBox;

    private ?float $highest;

    private ?float $lowest;

    private ?int $madeBets;

    private ?float $dailyReproductionChance;
    private ?float $positiveDays;
    private array $daysMadeBets;
    private array $daysOutcomes;

    /**
     * @var SeriesStatistic[]
     */
    private array $seriesStatistics;

    public function getBetRow(): ?SimpleBetRow
    {
        return $this->betRow;
    }

    public function setBetRow(?SimpleBetRow $betRow): BetRowSummaryData
    {
        $this->betRow = $betRow;
        return $this;
    }

    public function getCashBox(): ?float
    {
        return $this->cashBox;
    }

    public function setCashBox(?float $cashBox): BetRowSummaryData
    {
        $this->cashBox = $cashBox;
        return $this;
    }

    public function getHighest(): ?float
    {
        return $this->highest;
    }

    public function setHighest(?float $highest): BetRowSummaryData
    {
        $this->highest = $highest;
        return $this;
    }

    public function getLowest(): ?float
    {
        return $this->lowest;
    }

    public function setLowest(?float $lowest): BetRowSummaryData
    {
        $this->lowest = $lowest;
        return $this;
    }

    public function getMadeBets(): ?int
    {
        return $this->madeBets;
    }

    public function setMadeBets(?int $madeBets): BetRowSummaryData
    {
        $this->madeBets = $madeBets;
        return $this;
    }

    public function getDailyReproductionChance(): ?float
    {
        return $this->dailyReproductionChance;
    }

    public function setDailyReproductionChance(?float $dailyReproductionChance): BetRowSummaryData
    {
        $this->dailyReproductionChance = $dailyReproductionChance;
        return $this;
    }

    public function getDaysMadeBets(): array
    {
        return $this->daysMadeBets;
    }

    public function setDaysMadeBets(array $daysMadeBets): BetRowSummaryData
    {
        $this->daysMadeBets = $daysMadeBets;
        return $this;
    }

    public function getDaysOutcomes(): array
    {
        return $this->daysOutcomes;
    }

    public function setDaysOutcomes(array $daysOutcomes): BetRowSummaryData
    {
        $this->daysOutcomes = $daysOutcomes;
        return $this;
    }

    public function getSeriesStatistics(): array
    {
        return $this->seriesStatistics;
    }

    public function setSeriesStatistics(array $seriesStatistics): BetRowSummaryData
    {
        $this->seriesStatistics = $seriesStatistics;
        return $this;
    }

    public function getPositiveDays(): ?float
    {
        return $this->positiveDays;
    }

    public function setPositiveDays(?float $positiveDays): BetRowSummaryData
    {
        $this->positiveDays = $positiveDays;
        return $this;
    }
}
