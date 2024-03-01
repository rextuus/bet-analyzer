<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class ProcessSimulatorMessageHandler
{
    public function __construct(
        private TipicoBetSimulationService $betSimulationService,
        private SimulatorService $simulatorService
    )
    {
    }

    public function __invoke(ProcessSimulatorMessage $message): void
    {
        $simulators = $this->simulatorService->findBy(['id' => $message->getSimulatorId()]);
        if (count($simulators) !== 1) {
            return;
        }

        $this->betSimulationService->simulate($simulators[0]);
    }
}
