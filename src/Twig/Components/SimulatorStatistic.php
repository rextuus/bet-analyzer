<?php

namespace App\Twig\Components;

use App\Service\Tipico\SimulationStatisticService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SimulatorStatistic
{
    use DefaultActionTrait;

    #[LiveProp]
    public \App\Entity\BettingProvider\Simulator $simulator;

    #[LiveProp]
    public bool $initialLoad = true;

    public function __construct(private readonly SimulationStatisticService $simulationStatisticService)
    {
    }

    #[LiveAction]
    public function getStatistic(): array
    {
        if ($this->initialLoad) {
            $this->initialLoad = false;
            return [];
        }

        return $this->simulationStatisticService->getStatistics($this->simulator);
    }

}
