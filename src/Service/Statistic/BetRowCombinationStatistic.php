<?php
declare(strict_types=1);

namespace App\Service\Statistic;


class BetRowCombinationStatistic
{
    private float $total;
    private int $madeBets;
    private array $chartDataDays;
    private array $chartDataOutcomes;
    private array $betRowNames;

    private string $ident;

    public function getBetRowNames(): array
    {
        return $this->betRowNames;
    }

    public function setBetRowNames(array $betRowNames): BetRowCombinationStatistic
    {
        $this->betRowNames = $betRowNames;
        return $this;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): BetRowCombinationStatistic
    {
        $this->ident = $ident;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): BetRowCombinationStatistic
    {
        $this->total = $total;
        return $this;
    }

    public function getMadeBets(): int
    {
        return $this->madeBets;
    }

    public function setMadeBets(int $madeBets): BetRowCombinationStatistic
    {
        $this->madeBets = $madeBets;
        return $this;
    }

    public function getChartDataDays(): array
    {
        return $this->chartDataDays;
    }

    public function setChartDataDays(array $chartDataDays): BetRowCombinationStatistic
    {
        $this->chartDataDays = $chartDataDays;
        return $this;
    }

    public function getChartDataOutcomes(): array
    {
        return $this->chartDataOutcomes;
    }

    public function setChartDataOutcomes(array $chartDataOutcomes): BetRowCombinationStatistic
    {
        $this->chartDataOutcomes = $chartDataOutcomes;
        return $this;
    }
}
