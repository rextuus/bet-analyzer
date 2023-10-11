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
    private array $chartData;

    private Chart $chart;

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
