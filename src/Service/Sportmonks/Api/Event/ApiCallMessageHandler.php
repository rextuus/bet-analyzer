<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Event;

use App\Service\Sportmonks\Api\SportsmonkService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class ApiCallMessageHandler
{
    public function __construct(private SportsmonkService $sportsmonkService) { }

    public function __invoke(ApiCallMessage $message)
    {
        match($message->getApiRoute()){
            ApiRoute::ROUND => $this->callRoundApi($message->getMessageParameter()),
            ApiRoute::ODD => $this->callOddApi($message->getMessageParameter()),
            ApiRoute::SEASON => $this->callSeasonApi($message->getMessageParameter()),
            ApiRoute::STANDING => $this->callStandingApi($message->getMessageParameter())
        };
    }

    private function callRoundApi(int $page): void
    {
        $this->sportsmonkService->storeRoundsAndFixturesByPage($page);
    }

    private function callOddApi(int $fixtureId): void
    {
        $this->sportsmonkService->storeOddForFixture($fixtureId);
    }

    private function callSeasonApi(int $page): void
    {
        $this->sportsmonkService->storeSeasonsAndTeams($page);
    }

    /**
     * @deprecated
     */
    private function callStandingApi(int $roundId): void
    {
        $this->sportsmonkService->storeStandings($roundId);
    }
}
