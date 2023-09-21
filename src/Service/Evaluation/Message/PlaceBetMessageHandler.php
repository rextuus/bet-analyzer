<?php

namespace App\Service\Evaluation\Message;

use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PlaceBetMessageHandler
{
    public function __construct(
        readonly private BetRowCalculator $betRowCalculator,
        readonly private SpmFixtureService $fixtureService,
        readonly private BetRowOddFilterService $filterService,
        readonly private SimpleBetRowService $betRowService,
    )
    {
    }

    public function __invoke(PlaceBetMessage $message): void
    {
        $fixture = $this->fixtureService->findByApiId($message->getFixtureApiId());
        $filter = $this->filterService->findById($message->getBetRowOddFilterId());
        $betRow = $this->betRowService->findById($message->getBetRowId());
        $this->betRowCalculator->placeBet(
            $fixture,
            $filter,
            $message->getAccumulationVariant(),
            $betRow,
            $message->getBetRowVariant(),
            $message->getWager(),
            $message->isIncludeTax()
        );
    }
}
