<?php

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'InitDefaultOverUnderSimulatorsCommand',
    description: 'Add a short description for your command',
)]
class InitDefaultOverUnderSimulatorsCommand extends AbstractSimulatorCommand
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
            ->addArgument('searchBetOnTargetValue', InputArgument::OPTIONAL, 'Argument description')
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

        if ($targetBetOn !== BetOn::OVER && $targetBetOn !== BetOn::UNDER){
            throw new Exception('Invalid Beton [over|under]');
        }

        $targetValues = [0.5, 1.5, 2.5, 3.5, 4.5, 5.5];
        foreach ($targetValues as $targetValue) {
            $range = $this->generateFloatRange(1.0, 5.9, 0.1);

            $potentialSearchTargetName = $this->getPotentialSearchTargetName();

            $targetName = (string)floor($targetValue);
            if ($targetBetOn === BetOn::UNDER){
                $targetName = (string)round($targetValue);
            }

            foreach ($range as $item) {
                $ident = sprintf(
                    'ag_%s_search_%s%s_%s_%s_target_%s_[%s]',
                    OverUnderStrategy::IDENT,
                    $searchBetOn->name,
                    $potentialSearchTargetName,
                    str_replace('.', '', (string)$item[0] * 10),
                    str_replace('.', '', (string)$item[1] * 10),
                    $targetBetOn->name,
                    str_replace('.', '', $targetName),
                );

                $this->initOverUnderSimulators($ident, $item[0], $item[1], $searchBetOn, $targetBetOn, $targetValue);
            }
        }

        return Command::SUCCESS;
    }

    private function initOverUnderSimulators(
        string $identifier,
        float $min,
        float $max,
        BetOn $searchBetOn,
        BetOn $targetBetOn,
        float $targetValue
    ): void
    {
        if ($this->simulatorAlreadyExists($identifier)) {
            return;
        }

        $parameters = [
            AbstractSimulationProcessor::PARAMETER_MIN => $min,
            AbstractSimulationProcessor::PARAMETER_MAX => $max,
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => $searchBetOn,
            AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON => $targetBetOn,
            OverUnderStrategy::PARAMETER_TARGET_VALUE => $targetValue,
        ];

        $parameters = $this->addOptionalParameters($parameters);

        $simulationStrategyData = new SimulationStrategyData();
        $simulationStrategyData->setIdentifier(OverUnderStrategy::IDENT);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->storeSimulator($simulationStrategyData, $identifier);
    }
}
