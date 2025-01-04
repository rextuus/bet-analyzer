<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

use App\Command\WeekdayStatisticCommand;
use App\Service\Tipico\Content\SimulationStrategy\AdditionalProcessingIdent;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\BothTeamsScoreStrategy;
use App\Service\Tipico\SimulationProcessors\HeadToHeadStrategy;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsMessageHandler]
class InitSimulatorProcessingHandler
{
    public function __construct(private MessageBusInterface $messageBus, private SimulatorService $simulatorService)
    {
    }

    public function __invoke(InitSimulatorProcessingMessage $message): void
    {
        $strategies = [];
        $additional = [];
        $filterOptions = [];
        if ($message->getBulk() === SimulatorProcessBulk::THREE_WAY_SIMULATORS){
            $strategies = [SimpleStrategy::IDENT];
        }
        if ($message->getBulk() === SimulatorProcessBulk::OVER_UNDER_SIMULATORS){
            $strategies = [OverUnderStrategy::IDENT];
        }
        if ($message->getBulk() === SimulatorProcessBulk::BOTH_TEAMS_SCORE_SIMULATORS){
            $strategies = [BothTeamsScoreStrategy::IDENT];
        }
        if ($message->getBulk() === SimulatorProcessBulk::HEAD_TO_HEAD_SIMULATORS){
            $strategies = [HeadToHeadStrategy::IDENT];
        }

        if ($message->getBulk() === SimulatorProcessBulk::THREE_WAY_SIMULATORS_NSB){
            $strategies = [SimpleStrategy::IDENT];
            $additional = [AdditionalProcessingIdent::STOP_NEGATIVE_SERIES];
        }
        if ($message->getBulk() === SimulatorProcessBulk::BOTH_TEAMS_SCORE_SIMULATORS_NSB){
            $strategies = [BothTeamsScoreStrategy::IDENT];
            $additional = [AdditionalProcessingIdent::STOP_NEGATIVE_SERIES];
        }
        if ($message->getBulk() === SimulatorProcessBulk::HEAD_TO_HEAD_SIMULATORS_NSB){
            $strategies = [HeadToHeadStrategy::IDENT];
            $additional = [AdditionalProcessingIdent::STOP_NEGATIVE_SERIES];
        }
        if ($message->getBulk() === SimulatorProcessBulk::OVER_UNDER_SIMULATORS_NSB){
            $strategies = [OverUnderStrategy::IDENT];
            $additional = [AdditionalProcessingIdent::STOP_NEGATIVE_SERIES];
        }

        if ($message->getBulk() === SimulatorProcessBulk::THREE_WAY_SIMULATORS_RANDOM_INPUT) {
            $strategies = [SimpleStrategy::IDENT];
            $additional = [AdditionalProcessingIdent::USER_RANDOM_INPUT];
        }

        if ($message->getBulk() === SimulatorProcessBulk::TOP_WEEKDAY_SIMULATORS) {
            $strategies = [
                SimpleStrategy::IDENT,
                OverUnderStrategy::IDENT,
                BothTeamsScoreStrategy::IDENT,
                HeadToHeadStrategy::IDENT
            ];
            $filterOptions = [SimulatorService::FILTER_OPTION_IDENTIFIER => WeekdayStatisticCommand::POSTFIX];
        }

        $simulators = $this->simulatorService->findByStrategies($strategies, $additional, $filterOptions);

        foreach ($simulators as $simulator){
            $processMessage = new ProcessSimulatorMessage($simulator['id']);
            $this->messageBus->dispatch($processMessage);
        }
    }
}
