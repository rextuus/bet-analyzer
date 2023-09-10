<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api;

use App\Service\Sportmonks\Api\Event\ApiCallMessage;
use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Round\SpmRoundService;
use App\Service\Sportmonks\Content\Score\SpmScoreService;
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
        private readonly SpmScoreService $scoreService
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



        if ($response->getNextPage()) {
            $nextPageMessage = new ApiCallMessage(ApiRoute::ROUND);
            $nextPageMessage->setPage($response->getNextPage());

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

        if (count($undecorated) > 0){
            $nextPageMessage = new ApiCallMessage(ApiRoute::ODD);
            $nextPageMessage->setFixtureId($undecorated[0]->getApiId());

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
}
