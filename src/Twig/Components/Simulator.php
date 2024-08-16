<?php

namespace App\Twig\Components;

use App\Service\Tipico\SimulationStatisticService;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Simulator
{
    public \App\Entity\BettingProvider\Simulator $simulator;

    public function __construct(
        private readonly SimulationStatisticService $simulationStatisticService,
    )
    {
    }

    public function getDescription(): string
    {
        $strategy = $this->simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);
        $table = '<table>';
        foreach ($parameters as $key => $parameter) {
            $params = $parameter;
            if (is_array($parameter)) {
                $params = implode('-', $parameter);
            }
            $t = sprintf('<tr><td>%s</td><td>%s</td></tr>', $key, $params);
            $table = $table . $t;
        }
        $table = $table . '</table>';

        return $table;
    }

    public function getBetOutcomeChart(): Chart
    {
        return $this->simulationStatisticService->getBetOutcomeChart($this->simulator);
    }

    public function getCashBoxChart(bool $timeBased = true): Chart
    {
        return $this->simulationStatisticService->getCashBoxChart($this->simulator, $timeBased);
    }

    public function getDailyDistributionChart(): Chart
    {
        return $this->simulationStatisticService->getDailyDistributionChart($this->simulator);
    }

    public function getValueToWinDistributionChart(): Chart
    {
        return $this->simulationStatisticService->getValueToWinDistributionChart($this->simulator);
    }
}
