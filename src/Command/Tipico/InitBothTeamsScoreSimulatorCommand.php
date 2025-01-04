<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\BothTeamsScoreStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitBothTeamsScoreSimulatorCommand',
    description: 'Add a short description for your command',
)]
class InitBothTeamsScoreSimulatorCommand extends AbstractSimulatorCommand
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('ident');
        $min = $input->getArgument('min');
        $max = $input->getArgument('max');
        $betOn = $input->getArgument('betOn');

        if(!BetOn::tryFrom($betOn)){
            throw new \Exception('Invlaid Beton');
        }
        $beton = BetOn::from($betOn);
        if (!($beton === BetOn::BOTH_TEAMS_SCORE || $beton === BetOn::BOTH_TEAMS_SCORE_NOT)){
            throw new \Exception('Invlaid Beton');
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => BetOn::AWAY->value,
            AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON => $betOn,
        ];

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(BothTeamsScoreStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);

        return Command::SUCCESS;
    }
}
