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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultSimulators',
    description: 'Add a short description for your command',
)]
class InitDefaultSimulatorsCommand extends Command
{
    public function __construct(
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly SimulatorService $simulatorService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('variant', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('betOn', InputArgument::REQUIRED, 'Argument description')
            ->addOption('against', 'a', InputOption::VALUE_OPTIONAL, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $variant = $input->getArgument('variant');
        $betOn = $input->getArgument('betOn');

        if(!BetOn::tryFrom($betOn)){
            throw new \Exception('Invlaid Beton');
        }

        $strategyIdent = null;
        $against = null;
        if ($variant === 'simple'){
            $strategyIdent = SimpleStrategy::IDENT;
        }
        if ($variant === 'against'){
            $againstBeton = $input->getOption('against');
            if (!$againstBeton){
                $output->writeln('No against betOn given');
                return Command::FAILURE;
            }
            $against = BetOn::from($againstBeton);

            $strategyIdent = AgainstStrategy::IDENT;
        }

        if (!$strategyIdent){
            return Command::FAILURE;
        }
        $this->initSimpleSimulators(BetOn::from($betOn), $strategyIdent, $against);



        return Command::SUCCESS;
    }

    private function initSimpleSimulators(BetOn $betOn, string $strategyIdent, ?BetOn $against): void
    {
        $onIdent = $betOn->name;
        if ($against){
            $onIdent = $against->name;
        }

        $range = $this->generateFloatRange(1.0, 4.9, 0.1);
        foreach ($range as $item){
            $ident = sprintf(
                'ag_%s_search_%s_%s_%s_target_%s',
                $strategyIdent,
                $betOn->name,
                str_replace('.', '', (string) $item[0] * 10),
                str_replace('.', '', (string) $item[1] * 10),
                $onIdent,
            );

            $this->initSimpleSimulator($ident, $item[0], $item[1], $betOn, $strategyIdent, $against);
        }
    }

    private function initSimpleSimulator(string $identifier, float $min, float $max, BetOn $betOn, string $strategyIdent, ?BetOn $against): void
    {
        $sim = $this->simulatorService->findBy(['identifier' => $identifier]);
        if ($sim){
            return;
        }

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier($strategyIdent);

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_BET_ON => $betOn->value,
        ];

        if($strategyIdent === AgainstStrategy::IDENT){
            $parameters = [
                AbstractSimulationProcessor::PARAMETER_MIN => $min,
                AbstractSimulationProcessor::PARAMETER_MAX => $max,
                AgainstStrategy::PARAMETER_AGAINST => $against,
                AbstractSimulationProcessor::PARAMETER_BET_ON => $betOn,
                AgainstStrategy::PARAMETER_AGAINST_BOTH => 0,
            ];
        }

        $simulationStrategyData->setParameters(json_encode($parameters));

        $strategy = $this->simulationStrategyService->createByData($simulationStrategyData);

        $simulatorData = new SimulatorData();
        $simulatorData->setCashBox(100.0);
        $simulatorData->setIdentifier($identifier);
        $simulatorData->setStrategy($strategy);
        $simulatorData->setFixtures([]);
        $simulatorData->setPlacements([]);
        $simulatorData->setCurrentIn(1.0);
        $this->simulatorService->createByData($simulatorData);
    }

    public function generateFloatRange($start, $end, $step): array
    {
        $range = [];
        for ($i = $start; $i <= $end; $i += $step) {
            $range[] = [$i, $i + $step];
        }
        return $range;
    }
}
