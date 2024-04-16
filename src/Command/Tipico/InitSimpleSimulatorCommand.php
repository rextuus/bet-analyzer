<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitSimpleSimulatorCommand',
    description: 'InitSimpleSimulatorCommand',
)]
class InitSimpleSimulatorCommand extends AbstractSimulatorCommand
{
    private InputInterface $input;

    public function __construct(
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly SimulatorService $simulatorService,
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
            ->addArgument('searchBetOn', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('targetBetOn', InputArgument::REQUIRED, 'Argument description')
        ;
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        $identifier = $input->getArgument('ident');
        $min = $input->getArgument('min');
        $max = $input->getArgument('max');
        $searchBetOn = $input->getArgument('searchBetOn');
        $targetBetOn = $input->getArgument('targetBetOn');

        if(!BetOn::tryFrom($searchBetOn)){
            throw new \Exception('Invalid Beton');
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => $searchBetOn,
            AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON => $targetBetOn,
        ];
        $parameters = $this->addAdditionalParameters($parameters, $input);

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(SimpleStrategy::IDENT);
        $simulationStrategyData->setProcessingIdent($this->getPotentialProcessingIdent($this->input));
        $simulationStrategyData->setParameters(json_encode($parameters));

        $potentialNegativeBorderName = $this->getPotentialNegativeSeriesName($input);

        $this->storeSimulator($simulationStrategyData, $identifier.$potentialNegativeBorderName);

        return Command::SUCCESS;
    }
}
