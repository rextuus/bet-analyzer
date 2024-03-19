<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\AgainstStrategy;
use App\Service\Tipico\SimulationProcessors\BothTeamsScoreStrategy;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultBothTeamsScoreSimulatorsCommand',
    description: 'Add a short description for your command',
)]
class InitDefaultBothTeamsScoreSimulatorsCommand extends AbstractSimulatorCommand
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
            ->addArgument('betOn', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $betOn = $input->getArgument('betOn');

        if(!BetOn::tryFrom($betOn)){
            throw new \Exception('Invlaid Beton');
        }
        $beton = BetOn::from($betOn);
        if (!($beton === BetOn::BOTH_TEAMS_SCORE || $beton === BetOn::BOTH_TEAMS_SCORE_NOT)){
            throw new \Exception('Invlaid Beton');
        }

        $this->initBothTeamsScoreSimulators($beton);

        return Command::SUCCESS;
    }

    private function initBothTeamsScoreSimulators(BetOn $betOn): void
    {
        $onIdent = $betOn->name;

        $range = $this->generateFloatRange(1.0, 9.9, 0.1);
        foreach ($range as $item){
            $ident = sprintf(
                'ag_%s_search_%s_%s_%s_target_%s',
                BothTeamsScoreStrategy::IDENT,
                BetOn::HOME->name,
                str_replace('.', '', (string) $item[0] * 10),
                str_replace('.', '', (string) $item[1] * 10),
                $onIdent,
            );

            $this->initOverUnderSimulator($ident, $item[0], $item[1], $betOn);
        }
    }

    private function initOverUnderSimulator(string $identifier, float $min, float $max, BetOn $betOn): void
    {
        $sim = $this->simulatorService->findBy(['identifier' => $identifier]);
        if ($sim){
            return;
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_BET_ON => $betOn,
        ];

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(BothTeamsScoreStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);
    }

    public function generateFloatRange($start, $end, $step): array
    {
        $range = [];
        for ($i = $start; $i <= $end; $i += $step) {
            $range[] = [round($i, 2), round($i + $step, 2)];
        }
        return $range;
    }
}
