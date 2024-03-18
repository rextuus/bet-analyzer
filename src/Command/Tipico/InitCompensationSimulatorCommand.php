<?php

namespace App\Command\Tipico;

use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\CompensateLossStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitCompensationSimulator',
    description: 'Add a short description for your command',
)]
class InitCompensationSimulatorCommand extends AbstractSimulatorCommand
{
    public function __construct(
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly SimulatorService $simulatorService,
    )
    {
        parent::__construct($simulationStrategyService, $simulatorService);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('ident', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('min', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('max', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('betOn', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('defaultIn', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('ident');
        $min = $input->getArgument('min');
        $max = $input->getArgument('max');
        $betOn = $input->getArgument('betOn');
        $defaultIn = $input->getArgument('defaultIn');
        if (!$defaultIn){
            $defaultIn = 1.0;
        }

        $compensation = 0.0;

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            CompensateLossStrategy::PARAMETER_DEFAULT_IN => $defaultIn,
            CompensateLossStrategy::PARAMETER_COMPENSATION => $compensation,
            AbstractSimulationProcessor::PARAMETER_BET_ON => $betOn,
        ];

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(CompensateLossStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);

        return Command::SUCCESS;
    }
}
