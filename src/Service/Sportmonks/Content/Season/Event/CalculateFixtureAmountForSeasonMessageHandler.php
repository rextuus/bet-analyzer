<?php

namespace App\Service\Sportmonks\Content\Season\Event;

use App\Service\Sportmonks\Api\SportsmonkService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CalculateFixtureAmountForSeasonMessageHandler implements MessageHandlerInterface
{
    public function __construct(private SportsmonkService $sportsmonkService) { }

    public function __invoke(CalculateFixtureAmountForSeasonMessage $message)
    {
        $this->sportsmonkService->calculateExpectedFixtureAmountForSeason($message->getSeasonApiId());
    }
}
