<?php

namespace App\Command;

use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'DuplicateSimulatorCommand',
    description: 'Add a short description for your command',
)]
class DuplicateSimulatorCommand extends Command
{

    public function __construct(
        private SimulationStrategyService $simulationStrategyService,
        private SimulatorService $simulatorService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('simulator', InputArgument::REQUIRED, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $simulatorId = $input->getArgument('simulator');
        $simulator = $this->simulatorService->find($simulatorId);

        $simulatorData = (new SimulatorData())->initFromEntity($simulator);
        $simulatorStrategyData = (new SimulationStrategyData())->initFromEntity($simulator->getStrategy());
        $parameters = json_decode($simulatorStrategyData->getParameters(), true);
        $parameters[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS] = [Weekday::Monday];
        $simulatorStrategyData->setParameters(json_encode($parameters));

        $strategy = $this->simulationStrategyService->createByData($simulatorStrategyData);

        $simulatorData->setCashBox(100.0);
        $simulatorData->setIdentifier($simulatorData->getIdentifier() . '_monday');
        $simulatorData->setStrategy($strategy);
        $simulatorData->setFixtures([]);
        $simulatorData->setPlacements([]);
        $simulatorData->setCurrentIn(1.0);
        $this->simulatorService->createByData($simulatorData);

        return Command::SUCCESS;
    }

}
