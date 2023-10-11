<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto;

use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowDto
{
    private Chart $madeBetsChart;
    private Chart $outcomeChart;
    private float $cashBox;
    private string $filter;
    private int $madeBets;

    public function getMadeBetsChart(): Chart
    {
        return $this->madeBetsChart;
    }

    public function setMadeBetsChart(Chart $madeBetsChart): BetRowDto
    {
        $this->madeBetsChart = $madeBetsChart;
        return $this;
    }

    public function getOutcomeChart(): Chart
    {
        return $this->outcomeChart;
    }

    public function setOutcomeChart(Chart $outcomeChart): BetRowDto
    {
        $this->outcomeChart = $outcomeChart;
        return $this;
    }

    public function getCashBox(): float
    {
        return $this->cashBox;
    }

    public function setCashBox(float $cashBox): BetRowDto
    {
        $this->cashBox = $cashBox;
        return $this;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function setFilter(string $filter): BetRowDto
    {
        $this->filter = $filter;
        return $this;
    }

    public function getMadeBets(): int
    {
        return $this->madeBets;
    }

    public function setMadeBets(int $madeBets): BetRowDto
    {
        $this->madeBets = $madeBets;
        return $this;
    }
}
