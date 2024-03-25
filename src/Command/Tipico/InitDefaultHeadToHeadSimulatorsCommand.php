<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\HeadToHeadStrategy;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultHeadToHeadSimulatorsCommand',
    description: 'Add a short description for your command',
)]
class InitDefaultHeadToHeadSimulatorsCommand extends AbstractSimulatorCommand
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
        $this->validateDefaultParameters($input);
        $searchBetOn = BetOn::from($input->getArgument('searchBetOn'));
        $targetBetOn = BetOn::from($input->getArgument('targetBetOn'));

        if($targetBetOn !== BetOn::H2H_HOME && $targetBetOn !== BetOn::H2H_AWAY){
            throw new Exception('Invalid Beton [head_to_head_home|head_to_head_away]');
        }

        $rangeSteps = $this->generateFloatRange(1.0, 5.9, 0.1);
        foreach ($rangeSteps as $range){
            $potentialSearchTargetName = $this->getPotentialSearchTargetName();

            $ident = sprintf(
                'ag_%s_search_%s%s_%s_%s_target_%s',
                HeadToHeadStrategy::IDENT,
                $searchBetOn->name,
                $potentialSearchTargetName,
                str_replace('.', '', (string) $range[0] * 10),
                str_replace('.', '', (string) $range[1] * 10),
                $targetBetOn->name,
            );

            $this->initBothTeamsScoreSimulator($ident, $range[0], $range[1], $searchBetOn, $targetBetOn);
        }

        return Command::SUCCESS;
    }

    private function initBothTeamsScoreSimulator(
        string $identifier,
        float $min,
        float $max,
        BetOn $searchBetOn,
        BetOn $targetBetOn
    ): void
    {
        $sim = $this->simulatorService->findBy(['identifier' => $identifier]);
        if ($sim){
            return;
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => $searchBetOn,
            AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON => $targetBetOn,
        ];

        $parameters = $this->addOptionalParameters($parameters);

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(HeadToHeadStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);
    }
}
