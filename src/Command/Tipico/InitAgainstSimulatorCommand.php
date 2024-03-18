<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\AgainstStrategy;
use App\Service\Tipico\SimulationProcessors\CompensateLossStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'InitAgainstSimulator',
    description: 'Add a short description for your command',
)]
class InitAgainstSimulatorCommand extends AbstractSimulatorCommand
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
            ->addArgument('against', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('againstBoth', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('ident');
        $min = $input->getArgument('min');
        $max = $input->getArgument('max');
        $betOn = $input->getArgument('betOn');
        $against = $input->getArgument('against');
        $againstBoth = $input->getArgument('againstBoth');
        if (!$againstBoth){
            $againstBoth = false;
        }

        if(!BetOn::tryFrom($betOn) || !BetOn::tryFrom($against)){
            throw new \Exception('Invlaid Beton');
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AgainstStrategy::PARAMETER_AGAINST => $against,
            AgainstStrategy::PARAMETER_AGAINST_BOTH => $againstBoth,
            AbstractSimulationProcessor::PARAMETER_BET_ON => $betOn,
        ];

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(AgainstStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);

        return Command::SUCCESS;
    }
}
