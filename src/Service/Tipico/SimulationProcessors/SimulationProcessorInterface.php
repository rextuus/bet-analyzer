<?php

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Tipico\Simulation\Data\PlacementContainer;
use App\Service\Tipico\Simulation\Data\ProcessResult;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('simulation.processor')]
interface SimulationProcessorInterface
{
    public function getIdentifier(): string;

    /**
     * @param TipicoBet[] $fixtures
     * @param array<string, mixed> $parameters
     */
    public function calculate(Simulator $simulator, array $fixtures, array $parameters): ProcessResult;

    public function process(Simulator $simulator): PlacementContainer;
}