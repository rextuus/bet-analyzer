<?php
declare(strict_types=1);

namespace App\Command\Tipico;

use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsCommand(
    name: 'AbstractSimulatorCommand',
    description: 'Add a short description for your command',
)]
class AbstractSimulatorCommand extends Command
{
    public function __construct(
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly SimulatorService $simulatorService,
    )
    {
        parent::__construct();
    }

    protected function storeSimulator(SimulationStrategyData $data, string $identifier): void
    {
        $strategy = $this->simulationStrategyService->createByData($data);

        $simulatorData = new SimulatorData();
        $simulatorData->setCashBox(100.0);
        $simulatorData->setIdentifier($identifier);
        $simulatorData->setStrategy($strategy);
        $simulatorData->setFixtures([]);
        $simulatorData->setPlacements([]);
        $simulatorData->setCurrentIn(1.0);
        $this->simulatorService->createByData($simulatorData);
    }

    protected function simulatorAlreadyExists(string $identifier): bool
    {
        $sim = $this->simulatorService->findBy(['identifier' => $identifier]);
        if ($sim){
            return true;
        }
        return false;
    }

    protected function generateFloatRange($start, $end, $step): array
    {
        $range = [];
        for ($i = $start; $i <= $end; $i += $step) {
            $range[] = [round($i, 2), round($i + $step, 2)];
        }

        return $range;
    }
}
