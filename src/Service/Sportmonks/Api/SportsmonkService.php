<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api;

use App\Entity\SpmStanding;
use App\Service\Sportmonks\Api\Event\ApiCallMessage;
use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Round\SpmRoundService;
use App\Service\Sportmonks\Content\Score\SpmScoreService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Standing\SpmStandingService;
use App\Service\Sportmonks\Content\Team\SpmTeamService;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SportsmonkService
{

    public function __construct(
        private readonly SportsmonkApiGateway $sportsmonkApiGateway,
        private readonly MessageBusInterface $bus,
        private readonly SpmRoundService $roundService,
        private readonly SpmFixtureService $fixtureService,
        private readonly SpmOddService $oddService,
        private readonly SpmScoreService $scoreService,
        private readonly SpmTeamService $spmTeamService,
        private readonly SpmStandingService $spmStandingService,
        private readonly SpmSeasonService $seasonService,
        private readonly SpmLeagueService $leagueService
    ) {
    }

    public function storeRoundsAndFixturesByPage(int $page = null): void
    {
        $response = $this->sportsmonkApiGateway->getRoundsInclusiveFixtures($page);

        foreach ($response->getRounds() as $round) {
            if (!$this->roundService->findBy(['apiId' => $round->getApiId()])){
                $this->roundService->createByData($round);
            }
        }

        foreach ($response->getFixtures() as $fixture) {
            if ($fixture->isHasOdds()){
                if (!$this->fixtureService->findBy(['apiId' => $fixture->getApiId()])){
                    $this->fixtureService->createByData($fixture);
                }
            }
        }

        if ($response->getMessageParameter()){
            $this->dispatchNextPageMessage($response);
        }
    }

    public function storeOddForFixture(int $fixtureId): void
    {
        $response = $this->sportsmonkApiGateway->getOddsAndDetailsForFixtures($fixtureId);

        $storedOdds = $this->oddService->createMultipleByData($response->getOdds());
        $storedScores = $this->scoreService->createMultipleByData($response->getScores());

        $oddDecorated = false;
        if ($storedOdds > 0){
            $oddDecorated = true;
        }
        $scoreDecorated = false;
        if ($storedScores > 0){
            $scoreDecorated = true;
        }

        // save decorations
        $fixture = $this->fixtureService->findBy(['apiId' => $fixtureId])[0];
        $data = (new SpmFixtureData())->initFromEntity($fixture);
        $data->setOddDecorated($oddDecorated);
        $data->setScoreDecorated($scoreDecorated);
        $this->fixtureService->update($fixture, $data);


        $undecorated = $this->fixtureService->findBy(['oddDecorated' => false]);

        if (count($undecorated)){
            $response->setMessageParameter($undecorated[0]->getApiId());
            $this->dispatchNextPageMessage($response);
        }
    }

    public function storeLeagues()
    {
        $response = $this->sportsmonkApiGateway->getLeagues();
        foreach ($response as $leagueData){
            $this->leagueService->createByData($leagueData);
        }
    }

    public function storeStandings(int $roundId): void
    {
        $response = $this->sportsmonkApiGateway->getStandings($roundId);
        $this->spmStandingService->createMultipleByData($response->getStandings());

        $standings = $this->roundService->findRoundWithoutStandings();
dump($standings);
        if (count($standings)){
            $response->setMessageParameter($standings[0]->getApiId());
            $this->dispatchNextPageMessage($response);
        }
    }

    public function storeSeasonsAndTeams(int $page = null): void
    {
        $response = $this->sportsmonkApiGateway->getSeasonsAndTeams($page);

        foreach ($response->getTeams() as $team) {
            if (!$this->spmTeamService->findBy(['apiId' => $team->getApiId()])){
                $this->spmTeamService->createByData($team);
            }
        }

        foreach ($response->getSeasons() as $season) {
            if (!$this->seasonService->findBy(['apiId' => $season->getApiId()])){
                $this->seasonService->createByData($season);
            }
        }

        if ($response->getMessageParameter()){
            $this->dispatchNextPageMessage($response);
        }
    }

    /**
     * @param ResponseCanTriggerNextMessageInterface $response
     * @return void
     */
    public function dispatchNextPageMessage(ResponseCanTriggerNextMessageInterface $response): void
    {
            $nextPageMessage = new ApiCallMessage($response->getApiRoute());
            $nextPageMessage->setMessageParameter($response->getMessageParameter());

            if ($response->getWaitToContinue()) {
                $envelope = new Envelope($nextPageMessage, [
                    new DelayStamp(($response->getWaitToContinue() + 60) * 1000)
                ]);
                $this->bus->dispatch($envelope);
            } else {
                $this->bus->dispatch($nextPageMessage);
            }
    }
}
