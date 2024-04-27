<?php
declare(strict_types=1);

namespace App\Command\Tipico;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\SimulationStrategy\AdditionalProcessingIdent;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;


#[AsCommand(
    name: 'AbstractSimulatorCommand',
    description: 'Add a short description for your command',
)]
class AbstractSimulatorCommand extends Command
{
    protected ?string $searchBetOnTargetValue = null;

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
            ->addArgument('negativeSeriesBorder', InputArgument::OPTIONAL, '')
        ;
    }

    protected function validateDefaultParameters(InputInterface $input): bool
    {
        $searchBetOn = $input->getArgument('searchBetOn');
        $targetBetOn = $input->getArgument('targetBetOn');

        if (!BetOn::tryFrom($searchBetOn) || !BetOn::tryFrom($targetBetOn)) {
            throw new Exception('Invalid Beton');
        }
        $searchBetOn = BetOn::from($input->getArgument('searchBetOn'));
        $targetBetOn = BetOn::from($input->getArgument('targetBetOn'));

        // over/under search need an extra param for the target value
        $targets = [0.5, 1.5, 2.5, 3.5, 4.5, 5.5];
        if ($searchBetOn === BetOn::OVER || $searchBetOn === BetOn::UNDER) {
            $target = $input->getArgument('searchBetOnTargetValue');
            if (!$target || !in_array($target, $targets)){
                throw new Exception('OVER/UNDER search need a target');
            }
            $this->searchBetOnTargetValue = $target;
        }

        return true;
    }

    protected function addOptionalParameters(array $parameters): array
    {
        $parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON_TARGET] = $this->searchBetOnTargetValue;
        return $parameters;
    }

    protected function addAdditionalParameters(array $parameters, InputInterface $input): array
    {
        $negativeSeriesBorder = $input->getArgument('negativeSeriesBorder');
        if ($negativeSeriesBorder){
            $parameters[AbstractSimulationProcessor::PARAMETER_CURRENT_NEGATIVE_SERIES] = 0;
            $parameters[AbstractSimulationProcessor::PARAMETER_NEGATIVE_SERIES_BREAK_POINT] = $negativeSeriesBorder;
        }
        return $parameters;
    }

    protected function getPotentialSearchTargetName(): string
    {
        if ($this->searchBetOnTargetValue === null){
            return '';
        }
        return '_['.(string)round((float)$this->searchBetOnTargetValue).']';
    }

    protected function getPotentialNegativeSeriesName(InputInterface $input): string
    {
        $negativeSeriesBorder = $input->getArgument('negativeSeriesBorder');

        if ($negativeSeriesBorder){
            return '_nsb_'.$negativeSeriesBorder;
        }

        return '';
    }

    protected function getPotentialProcessingIdent(InputInterface $input): ?AdditionalProcessingIdent
    {
        $negativeSeriesBorder = $input->getArgument('negativeSeriesBorder');

        if ($negativeSeriesBorder){
            return AdditionalProcessingIdent::STOP_NEGATIVE_SERIES;
        }

        return AdditionalProcessingIdent::EMPTY;
    }

    protected function storeSimulator(SimulationStrategyData $data, string $identifier): void
    {
        $strategy = $this->simulationStrategyService->createByData($data);

        $simulatorData = new SimulatorData();
        $simulatorData->setCashBox(100.0);
        $simulatorData->setIdentifier($identifier);
        $simulatorData->setStrategy($strategy);
        $simulatorData->setFixtures([]);
        $simulatorData->setPlacements([]);
        $simulatorData->setCurrentIn(1.0);
        $this->simulatorService->createByData($simulatorData);
    }

    protected function simulatorAlreadyExists(string $identifier): bool
    {
        $sim = $this->simulatorService->findBy(['identifier' => $identifier]);
        if ($sim) {
            return true;
        }
        return false;
    }

    protected function generateFloatRange($start, $end, $step): array
    {
        $range = [];
        for ($i = $start; $i <= $end; $i += $step) {
            $range[] = [round($i, 2), round($i + $step, 2)];
        }

        return $range;
    }
}
