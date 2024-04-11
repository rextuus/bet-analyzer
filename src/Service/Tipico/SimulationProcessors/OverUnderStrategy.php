<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Entity\TipicoOverUnderOdd;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\Simulation\AdditionalProcessors\NegativeSeriesProcessor;
use App\Service\Tipico\Simulation\Data\ProcessResult;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class OverUnderStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'over_under';
    public const PARAMETER_TARGET_VALUE = 'target_value';

    public function __construct(
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly NegativeSeriesProcessor $negativeSeriesProcessor,
    )
    {
        parent::__construct(
            $placementService,
            $simulatorService,
            $simulationStrategyService,
            $tipicoBetService,
            $negativeSeriesProcessor
        );
    }

    public function calculate(Simulator $simulator, array $fixtures, array $parameters): ProcessResult
    {
        $targetValue = (float)$parameters[self::PARAMETER_TARGET_VALUE];
        $targetBetOn = BetOn::from($parameters[self::PARAMETER_TARGET_BET_ON]);

        $placementData = [];
        $fixturesActuallyUsed = [];
        foreach ($fixtures as $fixture) {
            $odds = array_filter(
                $fixture->getTipicoOverUnderOdds()->toArray(),
                function (TipicoOverUnderOdd $odd) use ($targetValue) {
                    return $odd->getTargetValue() === $targetValue;
                }
            );
            /** @var TipicoOverUnderOdd $odd */
            $odd = array_pop($odds);
            if (!$odd) {
                $fixturesActuallyUsed[] = $fixture;
                continue;
            }

            $usedOdd = $odd->getOverValue();
            $isWon = false;
            $result = $fixture->getEndScoreHome() + $fixture->getEndScoreAway();

            if ($result > $targetValue) {
                $isWon = true;
            }

            if ($targetBetOn === BetOn::UNDER) {
                $usedOdd = $odd->getUnderValue();
                $isWon = false;
                if ($result < $targetValue) {
                    $isWon = true;
                }
            }

            $placementData[] = $this->createPlacement(
                [$fixture],
                1.0,
                $usedOdd,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000),
                $isWon,
                $simulator
            );

            $fixturesActuallyUsed[] = $fixture;
        }

        $result = new ProcessResult();
        $result->setPlacementData($placementData);
        $result->setFixturesActuallyUsed($fixturesActuallyUsed);

        return $result;
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }
}
