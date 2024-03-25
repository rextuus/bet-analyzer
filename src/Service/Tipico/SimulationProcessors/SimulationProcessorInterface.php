<?php

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('simulation.processor')]
interface SimulationProcessorInterface
{
    public function getIdentifier(): string;

    public function calculate(Simulator $simulator): PlacementContainer;

    public function isHighCalculationAmount(Simulator $simulator): bool;
}