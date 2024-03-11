<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class ProcessSimulatorMessageHandler
{
    public function __construct(
        private readonly TipicoBetSimulationService $betSimulationService,
        private readonly SimulatorService $simulatorService,
        private readonly MessageBusInterface $messageBus,
        private readonly float $cashBoxLimit,
    )
    {
    }

    public function __invoke(ProcessSimulatorMessage $message): void
    {
        $simulators = $this->simulatorService->findBy(['id' => $message->getSimulatorId()]);
        if (count($simulators) !== 1) {
            return;
        }
        $simulator = $simulators[0];

        // check if multi messaging is necessary (more than 100 fixture to process)
        $reEnqueueMessage = $this->betSimulationService->isHighCalculationAmount($simulator);

        $this->betSimulationService->simulate($simulator);

        if ($reEnqueueMessage && $simulator->getCashBox() > $this->cashBoxLimit) {
            $this->messageBus->dispatch($message);
        }
    }
}
