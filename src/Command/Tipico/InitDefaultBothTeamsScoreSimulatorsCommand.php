<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\BothTeamsScoreStrategy;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultBothTeamsScoreSimulatorsCommand',
    description: 'Add a short description for your command',
)]
class InitDefaultBothTeamsScoreSimulatorsCommand extends AbstractSimulatorCommand
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
            ->addArgument('searchBetOn', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('targetBetOn', InputArgument::REQUIRED, 'Argument description')
        ;
        parent::configure();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        $this->validateDefaultParameters($input);
        $searchBetOn = BetOn::from($input->getArgument('searchBetOn'));
        $targetBetOn = BetOn::from($input->getArgument('targetBetOn'));

        if($targetBetOn !== BetOn::BOTH_TEAMS_SCORE && $targetBetOn !== BetOn::BOTH_TEAMS_SCORE_NOT){
            throw new Exception('Invalid Beton [both_teams_score|both_teams_score_not]');
        }

        $rangeSteps = $this->generateFloatRange(1.0, 5.9, 0.1);
        foreach ($rangeSteps as $range){
            $potentialSearchTargetName = $this->getPotentialSearchTargetName();
            $potentialNegativeBorderName = $this->getPotentialNegativeSeriesName($input);

            $ident = sprintf(
                'ag_%s_search_%s%s_%s_%s_target_%s%s',
                BothTeamsScoreStrategy::IDENT,
                $searchBetOn->name,
                $potentialSearchTargetName,
                str_replace('.', '', (string) $range[0] * 10),
                str_replace('.', '', (string) $range[1] * 10),
                $targetBetOn->name,
                $potentialNegativeBorderName
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
        $parameters = $this->addAdditionalParameters($parameters, $this->input);

        $parameters = $this->addOptionalParameters($parameters);

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(BothTeamsScoreStrategy::IDENT);
        $simulationStrategyData->setProcessingIdent($this->getPotentialProcessingIdent($this->input));
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);
    }
}
