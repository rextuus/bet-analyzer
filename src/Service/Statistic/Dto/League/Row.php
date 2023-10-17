<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto\League;

use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class Row
{
    private array $displayNames;
    private string $rowIds;

    private bool $addable;
    private array $chartData;

    private Chart $chart;

    private string  $buttonClass = '';

    public function getButtonClass(): string
    {
        return $this->buttonClass;
    }

    public function setButtonClass(string $buttonClass): Row
    {
        $this->buttonClass = $buttonClass;
        return $this;
    }
    public function isAddable(): bool
    {
        return $this->addable;
    }

    public function setAddable(bool $addable): Row
    {
        $this->addable = $addable;
        return $this;
    }

    public function getRowIds(): string
    {
        return $this->rowIds;
    }

    public function setRowIds(string $rowIds): Row
    {
        $this->rowIds = $rowIds;
        return $this;
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setChart(Chart $chart): Row
    {
        $this->chart = $chart;
        return $this;
    }

    public function getDisplayNames(): array
    {
        return $this->displayNames;
    }

    public function setDisplayNames(array $displayNames): Row
    {
        $this->displayNames = $displayNames;
        return $this;
    }

    public function getChartData(): array
    {
        return $this->chartData;
    }

    public function setChartData(array $chartData): Row
    {
        $this->chartData = $chartData;
        return $this;
    }
}
