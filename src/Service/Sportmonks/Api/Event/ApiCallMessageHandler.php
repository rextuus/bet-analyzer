<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Event;

use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
#[AsMessageHandler]
class ApiCallMessageHandler
{
    public function __construct(private SportsmonkService $sportsmonkService) { }

    public function __invoke(ApiCallMessage $message)
    {
        match($message->getApiRoute()){
            ApiRoute::ROUND => $this->callRoundApi($message->getPage()),
            ApiRoute::ODD => $this->callOddApi($message->getFixtureId())
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
}
