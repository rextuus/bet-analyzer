<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoBet;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\Simulation\AdditionalProcessors\AdditionalProcessorProvider;
use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use App\Service\Tipico\Simulation\Data\PlacementContainer;
use App\Service\Tipico\Simulation\Data\ProcessResult;
use DateTime;

abstract class AbstractSimulationProcessor
{
    public const PARAMETER_SEARCH_BET_ON = 'searchBetOn';
    public const PARAMETER_SEARCH_BET_ON_TARGET = 'searchBetOnTarget';
    public const PARAMETER_TARGET_BET_ON = 'targetBetOn';
    public const PARAMETER_MIN = 'min';
    public const PARAMETER_MAX = 'max';
    public const PARAMETER_NEGATIVE_SERIES_BREAK_POINT = 'negativeSeriesBreakPoint';
    public const PARAMETER_CURRENT_NEGATIVE_SERIES = 'currentNegativeSeries';
    public const PARAMETER_USE_RANDOM_INPUT = 'useRandomInput';
    public const PARAMETER_ALLOWED_WEEKDAYS = 'allowedWeekDays';

    public function __construct(
        private readonly TipicoPlacementService $placementService,
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly TipicoBetService $tipicoBetService,
        private readonly AdditionalProcessorProvider $additionalProcessorProvider,
    )
    {
    }

    public function process(Simulator $simulator): PlacementContainer
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);
        $fixtures = $this->tipicoBetService->getFixtureForSimulatorByFilter($simulator);

        $processResult = $this->calculate($simulator, $fixtures, $parameters);

        // placementData can be changed here if we want to cancel bets in negative series for example
        $additionalProcessResult = $this->processAdditionalSimulationStrategyParameters($processResult, $parameters);
        $placementData = $additionalProcessResult->getPlacementData();

        // store changes
        $container = $this->storePlacementsToDatabase($placementData);
        $this->storeSimulatorChangesToDatabase($simulator, $processResult->getFixturesActuallyUsed(), $container);

        if ($additionalProcessResult->isProcessedNegativeSeries()){
            $strategy = $simulator->getStrategy();
            $parameters = json_decode($simulator->getStrategy()->getParameters(), true);
            $parameters[self::PARAMETER_CURRENT_NEGATIVE_SERIES] = $additionalProcessResult->getCurrentNegativeSeries();

            $simulationStrategyData = (new SimulationStrategyData())->initFromEntity($strategy);
            $simulationStrategyData->setParameters(json_encode($parameters));

            $this->simulationStrategyService->update($strategy, $simulationStrategyData);
        }

        return $container;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function processAdditionalSimulationStrategyParameters(ProcessResult $processResult, array $parameters): AdditionalProcessResult
    {
        $result = new AdditionalProcessResult();
        $result->setPlacementData($processResult->getPlacementData());

        $additionalProcessors = $this->additionalProcessorProvider->getProcessorsByParameters($parameters);

        foreach ($additionalProcessors as $additionalProcessor) {
            $result = $additionalProcessor->process($result, $parameters);
        }

        return $result;
    }

    /**
     * @param TipicoPlacementData[] $dataObjects
     */
    protected function storePlacementsToDatabase(array $dataObjects): PlacementContainer
    {
        $placements = [];
        $cashBoxChange = 0.0;
        foreach ($dataObjects as $data) {
            $placement = $this->placementService->createByData($data);
            $placements[] = $placement;
            $cashBoxChange = $cashBoxChange - $placement->getInput();
            if ($placement->isWon()) {
                $cashBoxChange = $cashBoxChange + ($placement->getInput() * $placement->getValue());
            }
        }

        $container = new PlacementContainer();
        $container->setPlacements($placements);
        $container->setCashBoxChange($cashBoxChange);

        return $container;
    }

    /**
     * @param TipicoBet[] $fixturesActuallyUsed
     */
    protected function storeSimulatorChangesToDatabase(
        Simulator $simulator,
        array $fixturesActuallyUsed,
        PlacementContainer $container
    ): void
    {
        $simulatorData = (new SimulatorData())->initFromEntity($simulator);
        $simulatorData->setFixtures($fixturesActuallyUsed);
        $simulatorData->setPlacements($container->getPlacements());
        $simulatorData->setCashBox($simulator->getCashBox() + $container->getCashBoxChange());

        $this->simulatorService->update($simulator, $simulatorData);
    }

    /**
     * @param TipicoBet[] $fixtures
     */
    public function createPlacement(
        array $fixtures,
        float $input,
        float $value,
        DateTime $created,
        bool $isWon,
        Simulator $simulator,
    ): TipicoPlacementData
    {
        $data = new TipicoPlacementData();
        $data->setFixtures($fixtures);
        $data->setInput($input);
        $data->setValue($value);
        $data->setCreated($created);
        $data->setWon($isWon);
        $data->setSimulator($simulator);

        return $data;
    }

    public function calculate(Simulator $simulator, array $fixtures, array $parameters): ProcessResult
    {
        return new ProcessResult();
    }
}
