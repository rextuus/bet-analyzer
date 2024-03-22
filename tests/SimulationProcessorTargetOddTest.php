<?php
declare(strict_types=1);

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulationProcessorTargetOddTest extends TestCase
{
    private SimpleStrategy $simpleStrategy;

    private MockObject|SimpleStrategy $tipicoBetService;

    private MockObject|TipicoPlacementService $tipicoPlacementService;

    private MockObject|SimulatorService $simulatorService;

    private MockObject|SimulationStrategyService $simulationStrategyService;

    private TelegramMessageService|MockObject $telegramMessageService;

    private MockObject|TipicoBetSimulator $tipicoBetSimulator;

    public function __construct()
    {
        parent::__construct();

        $this->tipicoBetService = $this->getMockBuilder(TipicoBetService::class)->disableOriginalConstructor()->getMock();
        $this->tipicoPlacementService = $this->getMockBuilder(TipicoPlacementService::class)->disableOriginalConstructor()->getMock();
        $this->simulatorService = $this->getMockBuilder(SimulatorService::class)->disableOriginalConstructor()->getMock();
        $this->simulationStrategyService = $this->getMockBuilder(SimulationStrategyService::class)->disableOriginalConstructor()->getMock();
        $this->telegramMessageService = $this->getMockBuilder(TelegramMessageService::class)->disableOriginalConstructor()->getMock();
        $this->tipicoBetSimulator = $this->getMockBuilder(TipicoBetSimulator::class)->disableOriginalConstructor()->getMock();

        $this->simpleStrategy = new SimpleStrategy(
            $this->tipicoBetService,
            $this->tipicoPlacementService,
            $this->simulatorService,
            $this->simulationStrategyService,
            $this->telegramMessageService,
            $this->tipicoBetSimulator,
        );
    }

    public function testGetOddTargetFromParameters(): void
    {
        foreach (BetOn::cases() as $betOn){
            $parameters = [
                AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => $betOn->value,
            ];

            $targetOdd = $this->simpleStrategy->getOddTargetFromParameters($parameters);

            match($betOn){
                BetOn::HOME => $this->assertEquals('oddHome', $targetOdd),
                BetOn::DRAW => $this->assertEquals('oddDraw', $targetOdd),
                BetOn::AWAY => $this->assertEquals('oddHome', $targetOdd),
                BetOn::OVER => $this->assertEquals('oddHome', $targetOdd),
                BetOn::UNDER => $this->assertEquals('oddHome', $targetOdd),
                BetOn::BOTH_TEAMS_SCORE => $this->assertEquals('oddHome', $targetOdd),
                BetOn::BOTH_TEAMS_SCORE_NOT => $this->assertEquals('oddHome', $targetOdd),
                BetOn::H2H_HOME => $this->assertEquals('oddHome', $targetOdd),
                BetOn::H2H_AWAY => $this->assertEquals('oddAway', $targetOdd),
                default => $this->assertEquals('oddHome', $targetOdd)
            };
        }
    }
}
