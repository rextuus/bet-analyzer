<?php

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('additional.processor')]
interface AdditionalProcessorInterface
{
    public function getIdentifier(): string;

    /**
     * @param array<string, mixed> $parameters
     */
    public function process(AdditionalProcessResult $result, array $parameters): AdditionalProcessResult;
}