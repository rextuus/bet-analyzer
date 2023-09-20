<?php

namespace App\Service\Evaluation\Message;

use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InitBetRowMessageHandler
{
    public function __construct(
        readonly private BetRowCalculator $betRowCalculator
    )
    {
    }

    public function __invoke(InitBetRowMessage $message): void
    {
        $this->betRowCalculator->prepareInitBetRow(
            $message->getData(),
            $message->getBetOnVariant(),
            $message->getFrom(),
            $message->getTo(),
        );
    }
}
