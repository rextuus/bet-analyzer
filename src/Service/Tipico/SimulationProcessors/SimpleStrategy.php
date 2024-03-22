<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulator;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimpleStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'simple';

    public function __construct(
        protected readonly TipicoBetService $tipicoBetService,
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        protected readonly SimulationStrategyService $simulationStrategyService,
        protected readonly TelegramMessageService $telegramMessageService,
        private readonly TipicoBetSimulator $tipicoBetSimulator,
    )
    {
        parent::__construct($placementService, $simulatorService, $simulationStrategyService, $tipicoBetService);
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }

    public function calculate(Simulator $simulator): void
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);
        $targetOdd = Beton::from($parameters[self::PARAMETER_TARGET_BET_ON]);

        $fixtures = $this->getFittingFixtures(
            (float)$parameters[self::PARAMETER_MIN],
            (float)$parameters[self::PARAMETER_MAX],
            $this->getOddTargetFromParameters($parameters),
            $this->getUsedFixtureIds($simulator)
        );

        $placementData = [];
        $fixturesActuallyUsed = [];
        foreach ($fixtures as $fixture) {
            $isWon = false;
            if ($fixture->getResult() === $targetOdd){
                $isWon = true;
            }

            $value = $fixture->getOddHome();
            if ($targetOdd === BetOn::DRAW){
                $value = $fixture->getOddDraw();
            }
            if ($targetOdd === BetOn::AWAY){
                $value = $fixture->getOddAway();
            }

            $placementData[] = $this->tipicoBetSimulator->createPlacement(
                [$fixture],
                1.0,
                $value,
                (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp()/1000),
                $isWon,
                $simulator
            );

            $fixturesActuallyUsed[] = $fixture;
        }

        // store changes
        $container = $this->storePlacementsToDatabase($placementData);
        $this->storeSimulatorChangesToDatabase($simulator, $fixturesActuallyUsed, $container);

        if (count($placementData) > 0){
            $message = sprintf(
                '"%s" simulator placed %d bets and made a sales volume of %.2f. Current cash box: %.2f',
                $simulator->getIdentifier(),
                count($container->getPlacements()),
                $container->getCashBoxChange(),
                $simulator->getCashBox(),
            );

//            $this->telegramMessageService->sendMessageToTelegramFeed($message);
        }
    }
}
