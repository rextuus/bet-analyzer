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

    /**
     * Strategie-Parameter zur Anzeige (Listen werden mit „-“ verbunden). Escaping übernimmt Twig.
     *
     * @return array<string, string>
     */
    public function getParameters(): array
    {
        $parameters = json_decode($this->simulator->getStrategy()->getParameters(), true) ?? [];

        $result = [];
        foreach ($parameters as $key => $parameter) {
            $result[(string) $key] = is_array($parameter) ? implode('-', $parameter) : (string) $parameter;
        }

        return $result;
    }

    public function getBetOutcomeChart(): Chart
    {
        return $this->simulationStatisticService->getBetOutcomeChart($this->simulator);
    }

    public function getCashBoxChart(bool $timeBased = true): Chart
    {
        return $this->simulationStatisticService->getCashBoxChart($this->simulator, $timeBased);
    }

    /**
     * Kleiner Verlauf für die Tabellenzeile: ohne Legende und Datumsachse, feste Höhe.
     */
    public function getCompactCashBoxChart(): Chart
    {
        $chart = $this->getCashBoxChart(true);
        $chart->setOptions(array_replace_recursive($chart->getOptions(), [
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['display' => false]],
            'elements' => ['point' => ['radius' => 0]],
        ]));

        return $chart;
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
