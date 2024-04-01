<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use Symfony\UX\Chartjs\Model\Chart;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class WeekdayStatistic
{
    private Chart $chart;

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setChart(Chart $chart): WeekdayStatistic
    {
        $this->chart = $chart;
        return $this;
    }
}
