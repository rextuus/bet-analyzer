<?php
declare(strict_types=1);

use App\Entity\SimulationStrategy;
use App\Entity\Simulator;
use App\Entity\TipicoBet;
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
class SimpleSearchProcessorTest extends TestCase
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

    public function testCalculate(): void
    {
        $parameters = [
            AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON => BetOn::DRAW->value,
            AbstractSimulationProcessor::PARAMETER_MIN => 1.2,
            AbstractSimulationProcessor::PARAMETER_MAX => 1.3,
        ];

        $fixtureWins = new TipicoBet();
        $fixtureWins->setResult(BetOn::HOME);
        $fixtureWins->setOddHome(1.3);
        $fixtureWins->setOddDraw(4.3);
        $fixtureWins->setOddAway(5.2);
        $fixtures = [
            $fixtureWins
        ];
        $this->tipicoBetService->method('findInRange')->willReturn($fixtures);

        $simulatorStrategy = new SimulationStrategy();
        $simulatorStrategy->setParameters(json_encode($parameters));
        $simulator = new Simulator();
        $simulator->setStrategy($simulatorStrategy);
        $simulator->setIdentifier('TestSimulator');
        $simulator->setCashBox(100.0);

        $this->simpleStrategy->calculate($simulator);
    }
}
