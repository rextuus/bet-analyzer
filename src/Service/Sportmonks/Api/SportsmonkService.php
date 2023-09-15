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
use App\Service\Sportmonks\Content\Season\Statistic\Data\SeasonStatisticData;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
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
        private readonly SeasonStatisticService $seasonStatisticService,
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

    /**
     * @deprecated
     */
    public function storeStandings(int $roundId): void
    {
        $response = $this->sportsmonkApiGateway->getStandings($roundId);

        $this->spmStandingService->createMultipleByData($response->getStandings());

        $seasons = $this->seasonService->findSeasonsWithoutStanding();

        if (count($seasons)){
            if ($seasons[0]->getApiId() === $response->getNextRoundApiId()){
                $data = (new SpmSeasonData())->initFromEntity($seasons[0]);
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

    public function calculateExpectedFixtureAmountForSeason(SpmSeason $spmSeason): void
    {
        if ($spmSeason->getName() === '2023/2024') {
            return;
        }

        if ($this->seasonStatisticService->findBy(['seasonApiId' => $spmSeason->getApiId()])){
            return;
        }
        $league = $this->leagueService->findBy(['apiId' => $spmSeason->getLeagueApiId()])[0];
        $leagueName = '('.$spmSeason->getLeagueApiId().') '.$league->getName().' - '.$league->getShort().' ('.$league->getCountry().')';

        $newStandings = $this->spmStandingService->findBy(['seasonApiId' => $spmSeason->getApiId()]);
        if (count($newStandings) === 0){
            //1. api call: get standings for season => standings of the last round
            $response = $this->sportsmonkApiGateway->getStandings($spmSeason->getApiId());
            //2. store standings
            $this->spmStandingService->createMultipleByData($response->getStandings());
        }

        //3. check stage id = 2/3 (regular season)
        $newStandings = $this->spmStandingService->findBy(['seasonApiId' => $spmSeason->getApiId()]);
        if (!count($newStandings)){
//            throw new Exception('Could not store standings for season '. $spmSeason->getApiId());
            dump('Could not store standings for season '. $spmSeason->getApiId());

            $data = new SeasonStatisticData();
            $data->setMatchDays(0);
            $data->setExpectedMatchDays(0);
            $data->setExpectedMatchDaysAlternative(0);
            $data->setLeague($leagueName);
            $data->setSeasonApiId($spmSeason->getApiId());
            $data->setYear($spmSeason->getName());
            $data->setStage('-');
            $data->setTeams(0);
            $data->setDecoratedFixtures(0);
            $data->setIsRegularSeason(false);
            $data->setIsReliable(false);
            $data->setIsFaulty(true);
            $data->setNoStandingsAvailable(true);
            $data->setActuallyBetDecorated(0);
            $data->setManuallyConfirmed(false);

            $this->seasonStatisticService->createByData($data);
            return;
        }
        $standing = $newStandings[0];

        // check if regular
        $stage = $this->sportsmonkApiGateway->getStageBySeason($spmSeason->getApiId());

        $stageName = 'Regular Season';
        $isRegularSeason = true;
        if (array_key_exists('name', $stage)) {
            if ($stage['name'] !== 'Regular Season'){
                $stageName = $stage['name'];
                $isRegularSeason = false;
            }
        }

        //4. get the round by standing.round => name is the matchday <=> we know how many matchDays there are
        $round = $this->roundService->findBy(['apiId' => $standing->getRoundApiId()]);
        $matchDays = 0;
        if (count($round)){
            $matchDays = (int) $round[0]->getName();
        }

        //5. calculate matchday by teams (standings found for seasonId) and matchDays: teams * teams - teams <=> matchDays * teams/2
        $teams = count($newStandings);
        $expectedMatchdays = $teams * $teams - $teams;
        dump("Teams: $teams");
        $expectedMatchesAlternative = (int) ($matchDays * (((int)ceil($teams/2))/2));

        // store information
        $fixturesOfSeason = $this->fixtureService->findBy(['seasonApiId' => $spmSeason->getApiId()]);

        $data = new SeasonStatisticData();
        $data->setMatchDays($matchDays);
        $data->setExpectedMatchDays($expectedMatchdays);
        $data->setExpectedMatchDaysAlternative($expectedMatchesAlternative);
        $data->setLeague($leagueName);
        $data->setSeasonApiId($spmSeason->getApiId());
        $data->setYear($spmSeason->getName());
        $data->setStage($stageName);
        $data->setTeams($teams);
        $data->setDecoratedFixtures(count($fixturesOfSeason));
        $data->setIsRegularSeason($isRegularSeason);
        $data->setIsReliable(false);
        $data->setNoStandingsAvailable(false);
        $data->setActuallyBetDecorated(0);
        $data->setManuallyConfirmed(false);

        $isFaulty = false;
        if ($expectedMatchdays !== $expectedMatchesAlternative || $expectedMatchdays === 0 || $expectedMatchesAlternative === 0 || $stageName !== 'Regular Season'){
            $isFaulty = true;
        }
        $data->setIsFaulty($isFaulty);

        $this->seasonStatisticService->createByData($data);
    }
}
