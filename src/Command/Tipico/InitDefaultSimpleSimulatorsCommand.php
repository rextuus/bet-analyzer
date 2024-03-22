<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\AgainstStrategy;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultSimpleSimulators',
    description: 'Add a short description for your command',
)]
class InitDefaultSimpleSimulatorsCommand extends AbstractSimulatorCommand
{
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
            ->addArgument('searchBetOn', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('targetBetOn', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $searchBetOn = $input->getArgument('searchBetOn');
        $targetBetOn = $input->getArgument('targetBetOn');

        if(!BetOn::tryFrom($searchBetOn) || !BetOn::tryFrom($targetBetOn)){
            throw new Exception('Invalid Beton');
        }

        $searchBetOn = BetOn::from($searchBetOn);
        $targetBetOn = BetOn::from($targetBetOn);

        $rangeSteps = $this->generateFloatRange(1.0, 5.9, 0.1);
        foreach ($rangeSteps as $range){
            $ident = sprintf(
                'ag_%s_search_%s_%s_%s_target_%s',
                'ag_three_way',
                $searchBetOn->name,
                str_replace('.', '', (string) $range[0] * 10),
                str_replace('.', '', (string) $range[1] * 10),
                $targetBetOn->name,
            );

            $this->initSimpleSimulator($ident, $range[0], $range[1], $searchBetOn, $targetBetOn);
        }

        return Command::SUCCESS;
    }

    public function initSimpleSimulator(
        string $identifier,
        float $min,
        float $max,
        BetOn $searchBetOn,
        BetOn $targetBetOn
    ): void
    {
        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => $searchBetOn,
            AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON => $targetBetOn,
        ];

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(SimpleStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);
    }
}
