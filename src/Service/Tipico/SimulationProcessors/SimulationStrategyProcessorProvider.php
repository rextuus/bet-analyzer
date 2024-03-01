<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationStrategyProcessorProvider
{
    public function __construct(
        /**
         * @var iterable<int, SimulationProcessorInterface> $processors
         */
        #[TaggedIterator('simulation.processor')]
        private iterable $processors,
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
}
