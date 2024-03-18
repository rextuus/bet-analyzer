<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AgainstStrategy;
use App\Service\Tipico\SimulationProcessors\CombineStrategy;
use App\Service\Tipico\SimulationProcessors\CompensateLossStrategy;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class InitSimulatorProcessingHandler
{
    public function __construct(private MessageBusInterface $messageBus, private SimulatorService $simulatorService)
    {
    }

    public function __invoke(InitSimulatorProcessingMessage $message): void
    {
        $strategies = [];
        if ($message->getBulk() === SimulatorProcessBulk::THREE_WAY_SIMULATORS){
            $strategies = [SimpleStrategy::IDENT, CombineStrategy::IDENT, CompensateLossStrategy::IDENT, AgainstStrategy::IDENT];
        }
        if ($message->getBulk() === SimulatorProcessBulk::OVER_UNDER_SIMULATORS){
            $strategies = [OverUnderStrategy::IDENT];
        }

        $simulators = $this->simulatorService->findByStrategies($strategies);

        foreach ($simulators as $simulator){
            $processMessage = new ProcessSimulatorMessage($simulator['id']);
            $this->messageBus->dispatch($processMessage);
        }
    }
}
