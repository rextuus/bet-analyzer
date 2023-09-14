<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api;

use App\Entity\SpmSeason;
use App\Service\Sportmonks\Api\Event\ApiCallMessage;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\Fixture\InvalidFixture\Data\InvalidFixtureData;
use App\Service\Sportmonks\Content\Fixture\InvalidFixture\InvalidFixtureService;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Round\SpmRoundService;
use App\Service\Sportmonks\Content\Score\SpmScoreService;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Standing\SpmStandingService;
use App\Service\Sportmonks\Content\Team\SpmTeamService;
use DateTime;
use Exception;
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
        private readonly SpmLeagueService $leagueService,
        private readonly InvalidFixtureService $invalidFixtureService,
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

        // if decoration could not be done due to no odds mark the fixture as invalid
        if (!$oddDecorated) {
            $invalidFixtureData = new InvalidFixtureData();
            $invalidFixtureData->setFixtureApiId($fixtureId);
            $invalidFixtureData->setDecorationAttempt(new DateTime());
            $invalidFixtureData->setReason('no_odds_found');
            $this->invalidFixtureService->createByData($invalidFixtureData);
        }

        // get next decoratable fixture
        $undecorated = $this->fixtureService->findNextUndecoratedFixture();

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

        $seasons = $this->seasonService->findSeasonsWithoutStanding();

        if (count($seasons)){
            if ($seasons[0]->getApiId() === $response->getNextRoundApiId()){
                $data = (new SpmSeasonData())->initFromEntity($seasons[0]);
                $data->setStandingsAvailable(false);
                $this->seasonService->update($seasons[0], $data);

                $seasons = $this->seasonService->findSeasonsWithoutStanding();
            }

            $response->setMessageParameter($seasons[0]->getApiId());
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

    public function calculateExpectedFixtureAmountForSeason(SpmSeason $spmSeason)
    {
        if ($spmSeason->getName() === '2023/2024') {
            return;
        }

        $newStandings = $this->spmStandingService->findBy(['seasonApiId' => $spmSeason->getApiId()]);
        if (count($newStandings) === 0){
            //1. api call: get standings for season => standings of the last round
            $response = $this->sportsmonkApiGateway->getStandings($spmSeason->getApiId());
            //2. store standings
            $this->spmStandingService->createMultipleByData($response->getStandings());
        }

        //3. check stage id = 2/3 (regular season)
        $newStandings = $this->spmStandingService->findBy(['seasonApiId' => $spmSeason->getApiId()]);
        $isRegularSeason = false;
        if (count($newStandings) === 0){
            throw new Exception('Could not store standings for season '. $spmSeason->getApiId());
        }
        $standing = $newStandings[0];

        $regularSeasonStages = [2, 3, 502, 1088];
        if (in_array($standing->getStageApiId(), $regularSeasonStages)) {
            $isRegularSeason = true;
        }

        if (!$isRegularSeason) {
            dump("Work with standings which are not in stage regular season: " . $standing->getStageApiId());
        }

        //4. get the round by standing.round => name is the matchday <=> we know how many matchdays there are
        $round = $this->roundService->findBy(['apiId' => $standing->getRoundApiId()])[0];
        $matchDay = (int) $round->getName();
        //5. calculate matchday by teams (standings found for seasonId) and matchdays: teams * teams - teams <=> matchdays * teams/2
        $teams = count($newStandings);
        $matchDays = $teams * $teams - $teams;
        $matchDaysAlternative = $matchDay * ($teams/2);
        dump($matchDays);
        dump($matchDaysAlternative);

        if ($matchDays !== $matchDaysAlternative){
            // seems tricky. Check if there are any fixtures with odds for this season which are worth to add more work here
            $fixturesOfSeason = $this->fixtureService->findBy(['seasonApiId' => $spmSeason->getApiId(), 'oddDecorated' => true]);

            dump("SeasonApi: ".$spmSeason->getApiId());
            dump("LeagueApi: ".$spmSeason->getLeagueApiId());
            dump("Year: ".$spmSeason->getName());
            dump("Matchdays: $matchDay");
            dump("Teams: $teams");
            dump("Fixtures with odds: ".count($fixturesOfSeason));

        }

//        $seasons = $this->seasonService->getSeasonFixtureAmountBasedOnStanding();
//
//        foreach ($seasons as $season){
//            $teams = $season['teams'];
//            /** @var SpmSeason $season */
//            $season = $season['season'];
//
//
//            $expectedFixtures = $this->calculateExpectedFixtures($teams);
//
//            if ($expectedFixtures === 0 && $season->getStartingAt() > new DateTime('01-01-2016')
//                && $season->getStartingAt() < new DateTime('04-01-2023')
//            ){
//                $t = sprintf(
//                    '%s(%d): %s/%s',
//                    $this->leagueService->findBy(['apiId' => $season->getLeagueApiId()])[0]->getName(),
//                    $season->getApiId(),
//                    $season->getStartingAt()->format('Y'),
//                    $season->getEndingAt()->format('Y')
//                );
//                dump($t);
//                dump(sprintf('League has %d teams and %d total game', $teams, ($teams * $teams) - $teams));
//                dump();
//            }
//
//            $seasonData = (new SpmSeasonData())->initFromEntity($season);
//            $seasonData->setExpectedFixtures($expectedFixtures);
////            $this->seasonService->update($season, $seasonData);
//        }
    }

    private function calculateExpectedFixtures(int $teams): int
    {
        $expectedTotals = [380, 306];

        $expectedHeadToHeads = ($teams * $teams) - $teams;
        if (!in_array($expectedHeadToHeads, $expectedTotals)){
            return 0;
        }

        return $expectedHeadToHeads;
    }
}
