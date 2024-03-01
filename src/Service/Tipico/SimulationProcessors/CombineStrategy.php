<?php
declare(strict_types=1);

namespace App\Service\Tipico\SimulationProcessors;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulator;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class CombineStrategy extends AbstractSimulationProcessor implements SimulationProcessorInterface
{
    public const IDENT = 'combine';
    public const PARAMETER_MIN = 'min';
    public const PARAMETER_MAX = 'max';
    public const PARAMETER_COMBINATION_AMOUNT = 'combinationAmount';

    public function __construct(
        private readonly TipicoBetService $tipicoBetService,
        protected readonly TipicoPlacementService $placementService,
        protected readonly SimulatorService $simulatorService,
        private readonly TipicoBetSimulator $tipicoBetSimulator,
        protected readonly TelegramMessageService $telegramMessageService,
    )
    {
        parent::__construct($placementService, $simulatorService);
    }

    public function getIdentifier(): string
    {
        return self::IDENT;
    }

    public function calculate(Simulator $simulator): void
    {
        $parameters = json_decode($simulator->getStrategy()->getParameters(), true);

        $fixtures = $this->getFittingFixtures(
            (float) $parameters[self::PARAMETER_MIN],
            (float) $parameters[self::PARAMETER_MAX],
            $this->getOddTargetFromParameters($parameters),
            $this->getUsedFixtureIds($simulator)
        );

        // have we enough for a bet combination
        if (count($fixtures) >= $parameters[self::PARAMETER_COMBINATION_AMOUNT]){
            $fixturesActuallyUsed = [];

            $used = 0;
            $fixturesToCombine = [];

            $dataObjects = [];

            while ($used < count($fixtures)){
                $fixturesToCombine[] = $fixtures[$used];
                $used++;

                if ($used % $parameters[self::PARAMETER_COMBINATION_AMOUNT] === 0){
                    $dataObjects[] = $this->tipicoBetSimulator->combineFixtures($simulator, $fixturesToCombine, BetOn::from($parameters[self::PARAMETER_BET_ON]));
                    $fixturesActuallyUsed = array_merge($fixturesActuallyUsed, $fixturesToCombine);
                    $fixturesToCombine = [];
                }
            }

            $container = $this->storePlacementsToDatabase($dataObjects);

            // handle the simulator
            $this->storeSimulatorChangesToDatabase($simulator, $fixturesActuallyUsed, $container);

            $message = sprintf(
                '"%s" simulator placed %d bets and made a sales volume of %.2f. Current cash box: %.2f',
                $simulator->getIdentifier(),
                count($container->getPlacements()),
                $container->getCashBoxChange(),
                $simulator->getCashBox(),
            );

            $this->telegramMessageService->sendMessageToTelegramFeed($message);
        }
    }

    /**
     * @return TipicoBet[]
     */
    private function getFittingFixtures(float $min, float $max, string $targetOddColumn, array $alreadyUsed): array
    {
        return $this->tipicoBetService->findInRange($min, $max, $targetOddColumn, $alreadyUsed);
    }
}
