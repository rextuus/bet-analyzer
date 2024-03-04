<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\SimulationStrategy;
use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\Data\SimulationStrategyData;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulator;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class AbstractSimulationProcessor
{
    public const PARAMETER_BET_ON = 'betOn';
    public const PARAMETER_MIN = 'min';
    public const PARAMETER_MAX = 'max';

    public function __construct(
        private readonly TipicoPlacementService $placementService,
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStrategyService $simulationStrategyService,
        private readonly TipicoBetService $tipicoBetService,
    )
    {
    }

    protected function getOddTargetFromParameters(array $parameters): string
    {
        $targetOddColumn = 'oddDraw';
        if ($parameters[self::PARAMETER_BET_ON] === '1') {
            $targetOddColumn = 'oddHome';
        }

        if ($parameters[self::PARAMETER_BET_ON] === '2') {
            $targetOddColumn = 'oddAway';
        }

        return $targetOddColumn;
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
     * @return int[]
     */
    protected function getUsedFixtureIds(Simulator $simulator): array
    {
        return array_map(
            function (TipicoBet $tipicoBet){
                return $tipicoBet->getId();
            },
            $simulator->getFixtures()->toArray()
        );
    }

    /**
     * @return TipicoBet[]
     */
    protected function getFittingFixtures(float $min, float $max, string $targetOddColumn, array $alreadyUsed): array
    {
        return $this->tipicoBetService->findInRange($min, $max, $targetOddColumn, $alreadyUsed);
    }

    protected function storeSimulatorStrategyChanges(Simulator $simulator, float $compensationIn): void
    {
        $strategy = $simulator->getStrategy();
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);
        $parameters[CompensateLossStrategy::PARAMETER_COMPENSATION] = $compensationIn;

        $simulationStrategyData = (new SimulationStrategyData())->initFromEntity($strategy);
        $simulationStrategyData->setParameters(json_encode($parameters));

        $this->simulationStrategyService->update($strategy, $simulationStrategyData);
    }
}