<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Traversable;


class SimulationStrategyProcessorProvider
{
    public function __construct(
        /**
         * @var iterable<int, SimulationProcessorInterface>|Traversable $processors
         */
        #[TaggedIterator('simulation.processor')]
        private readonly iterable $processors,
    )
    {
    }

    public function getProcessorByIdent(string $ident): ?SimulationProcessorInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->getIdentifier() === $ident) {
                return $processor;
            }
        }

        return null;
    }

    /**
     * @return SimulationProcessorInterface[]
     */
    public function getAllProcessors(): array
    {
        return iterator_to_array($this->processors);
    }

    /**
     * @return string[]
     */
    public function getIdents():array
    {
        return array_map(
          function (SimulationProcessorInterface $processor){
              return $processor->getIdentifier();
          },
            iterator_to_array($this->processors)
        );
    }
}
