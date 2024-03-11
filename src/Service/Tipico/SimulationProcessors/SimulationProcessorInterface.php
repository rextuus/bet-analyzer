<?php

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\SimulationStrategy;
use App\Entity\Simulator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('simulation.processor')]
interface SimulationProcessorInterface
{
    public function getIdentifier(): string;

    public function calculate(Simulator $simulator): void;

    public function isHighCalculationAmount(Simulator $simulator): bool;
}