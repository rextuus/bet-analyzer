<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class FavoriteListPeriodStatisticData
{
    private array $rawValues;
    private array $betDistribution;

    private Chart $dailyChart;

    private Chart $totalChart;

    private float $totalBalance;
    private int $totalBets;

    private float $dailyMin;
    private string $dailyMinDate;
    private float $dailyMax;
    private string $dailyMaxDate;

    public function getRawValues(): array
    {
        return $this->rawValues;
    }

    public function setRawValues(array $rawValues): FavoriteListPeriodStatisticData
    {
        $this->rawValues = $rawValues;
        return $this;
    }

    public function getBetDistribution(): array
    {
        return $this->betDistribution;
    }

    public function setBetDistribution(array $betDistribution): FavoriteListPeriodStatisticData
    {
        $this->betDistribution = $betDistribution;
        return $this;
    }

    public function getDailyChart(): Chart
    {
        return $this->dailyChart;
    }

    public function setDailyChart(Chart $dailyChart): FavoriteListPeriodStatisticData
    {
        $this->dailyChart = $dailyChart;
        return $this;
    }

    public function getTotalChart(): Chart
    {
        return $this->totalChart;
    }

    public function setTotalChart(Chart $totalChart): FavoriteListPeriodStatisticData
    {
        $this->totalChart = $totalChart;
        return $this;
    }

    public function getTotalBalance(): float
    {
        return $this->totalBalance;
    }

    public function setTotalBalance(float $totalBalance): FavoriteListPeriodStatisticData
    {
        $this->totalBalance = $totalBalance;
        return $this;
    }

    public function getTotalBets(): int
    {
        return $this->totalBets;
    }

    public function setTotalBets(int $totalBets): FavoriteListPeriodStatisticData
    {
        $this->totalBets = $totalBets;
        return $this;
    }

    public function getDailyMin(): float
    {
        return $this->dailyMin;
    }

    public function setDailyMin(float $dailyMin): FavoriteListPeriodStatisticData
    {
        $this->dailyMin = $dailyMin;
        return $this;
    }

    public function getDailyMinDate(): string
    {
        return $this->dailyMinDate;
    }

    public function setDailyMinDate(string $dailyMinDate): FavoriteListPeriodStatisticData
    {
        $this->dailyMinDate = $dailyMinDate;
        return $this;
    }

    public function getDailyMax(): float
    {
        return $this->dailyMax;
    }

    public function setDailyMax(float $dailyMax): FavoriteListPeriodStatisticData
    {
        $this->dailyMax = $dailyMax;
        return $this;
    }

    public function getDailyMaxDate(): string
    {
        return $this->dailyMaxDate;
    }

    public function setDailyMaxDate(string $dailyMaxDate): FavoriteListPeriodStatisticData
    {
        $this->dailyMaxDate = $dailyMaxDate;
        return $this;
    }
}
