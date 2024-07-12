<?php

declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Traversable;


class AdditionalProcessorProvider
{
    public function __construct(
        /**
         * @var iterable<int, AdditionalProcessorInterface>|Traversable $processors
         */
        #[TaggedIterator('additional.processor')]
        private readonly iterable $processors,
    ) {
    }


    /**
     * @param array<string, mixed> $parameters
     * @return array<AdditionalProcessorInterface>
     */
    public function getProcessorsByParameters(array $parameters): array
    {
        $processorsToUse = [];
        foreach ($this->processors as $processor) {
            if (array_key_exists($processor->getIdentifier(), $parameters)) {
                $processorsToUse[] = $processor;
            }
        }

        return $processorsToUse;
    }
}
