<?php


namespace App\Service\Sportmonks\Api;


use App\Entity\FixtureOdd;
use App\Service\Api\Response\ClubStanding;
use App\Service\Api\Response\FixtureOddResponse;
use App\Service\Api\Response\FixtureResponse;
use App\Service\Api\Response\RoundResponse;
use App\Service\Api\Response\StandingResponse;
use App\Service\Setting\FootballApiManagerService;
use App\Service\Sportmonks\Api\Event\ApiCallMessage;
use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\League\Data\SpmLeagueData;
use App\Service\Sportmonks\Content\Odd\Data\SpmOddData;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;
use App\Service\Sportmonks\Content\Score\Data\SpmScoreData;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SportsmonkApiGateway
{
    private const BASE_URI = 'https://api.sportmonks.com/v3/football/';
    private const API_KEY_OLD = 'm2t50hFJHSBPoP5fn9JQFf0Xx7sPbv4MC0M3kScaZuhh2V2dJ1oPYEkZQ1FZ';
    private const API_KEY = 'GDWgZMLWo6YsYnco6T4J5xO3Sy9noQvPx0qi14h9N3wN0MIFKo6TAGuRfgXM';

    public function __construct(private readonly GuzzleClientFactory $clientFactory, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @return SpmLeagueData[]
     */
    public function getLeagues(): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY]
        ];

        try {
            $response = $client->request('GET', 'leagues', $options);
        } catch (GuzzleException $e) {
            dump($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $data = [];
        foreach ($response['data'] as $entry){
            $data[] = (new SpmLeagueData())->initFromApiResponse($entry);
        }

        return $data;
    }

    public function getRoundsInclusiveFixtures(int $page = null): RoundAndFixtureResponse
    {
        //https://api.sportmonks.com/v3/football/rounds?api_token={{api_token}}&include=fixtures;
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $queryParams = ['api_token' => self::API_KEY, 'include' => 'fixtures'];
        if ($page){
            $queryParams['page'] = $page;
        }

        $options = [
            'query' => $queryParams
        ];

        try {
            $response = $client->request('GET', 'rounds', $options);
        } catch (GuzzleException $e) {
            dump($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $nextPage = null;
        $pagination = $response['pagination'];
        if ($pagination['has_more']){
            $nextPage = $pagination['current_page'] + 1;
        }

        $limits = $response['rate_limit'];
        $waitToContinue = null;
        if ($limits['remaining'] === 1){
            $waitToContinue = $limits['resets_in_seconds'];
        }

        $rounds = [];
        $fixtures = [];
        foreach ($response['data'] as $round){
            $fixturesData = $round['fixtures'];
            foreach ($fixturesData as $fixture){
                $fixtures[] = (new SpmFixtureData())->initFromApiResponse($fixture);
            }
            $rounds[] = (new SpmRoundData())->initFromApiResponse($round);
        }

        return new RoundAndFixtureResponse($rounds, $fixtures, $nextPage, $waitToContinue);
    }

    public function getOddsForFixtures(int $fixtureId): OddAndScoreResponse
    {
        //https://api.sportmonks.com/v3/football/rounds?api_token={{api_token}}&include=fixtures;
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $queryParams = ['api_token' => self::API_KEY];

        $options = [
            'query' => $queryParams
        ];

        try {
            $response = $client->request('GET', 'odds/pre-match/fixtures/'.$fixtureId, $options);
        } catch (GuzzleException $e) {
            dump($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $limits = $response['rate_limit'];
        $waitToContinue = null;
        if ($limits['remaining'] === 1){
            $waitToContinue = $limits['resets_in_seconds'];
        }

        $odds = [];
        foreach ($response['data'] as $odd){
            $odds[] = (new SpmOddData())->initFromApiResponse($odd);
        }

        return new OddAndScoreResponse($odds, $waitToContinue);
    }

    public function getOddsAndDetailsForFixtures(int $fixtureId): OddAndScoreResponse
    {
        //https://api.sportmonks.com/v3/football/rounds?api_token={{api_token}}&include=fixtures;
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $queryParams = ['api_token' => self::API_KEY, 'include' => 'scores;odds'];

        $options = [
            'query' => $queryParams
        ];

        try {
            $response = $client->request('GET', 'fixtures/'.$fixtureId, $options);
        } catch (GuzzleException $e) {
            dump($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $limits = $response['rate_limit'];
        $waitToContinue = null;
        if ($limits['remaining'] === 1){
            $waitToContinue = $limits['resets_in_seconds'];
        }

        $oddData = $response['data']['odds'];
        $scoreData = $response['data']['scores'];

        $scores = [];
        foreach ($scoreData as $score){
            $scores[] = (new SpmScoreData())->initFromApiResponse($score);
        }

        $odds = [];
        foreach ($oddData as $odd){
            $odds[] = (new SpmOddData())->initFromApiResponse($odd);
        }

        return new OddAndScoreResponse($odds, $scores, $waitToContinue);
    }



        /////////////////////////////////////////////

    public function getLeagueById(int $sportMonksId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY]
        ];

        try {
            $response = $client->request('GET', 'leagues/'.$sportMonksId, $options);
        } catch (GuzzleException $e) {
            dump($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['data'];
    }

    /**
     * @return array
     */
    public function getAvailableSeasons(int $page): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY, 'page' => $page]
        ];

        try {
            $response = $client->request('GET', 'seasons', $options);
        } catch (GuzzleException) {
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (!array_key_exists('data', $response)){
            return [];
    }
        return $response['data'];
    }

    /**
     * @return array
     */
    public function getAvailableSeasonsPageCall(): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY, 'has_more' => 1]
        ];

        try {
            $response = $client->request('GET', 'seasons', $options);
        } catch (GuzzleException $e) {
            dd($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['pagination'];
    }

    /**
     * @return array
     */
    public function getClubsForSeason(int $seasonId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY]
        ];

        try {
            $response = $client->request('GET', 'teams/seasons/'.$seasonId, $options);
        } catch (GuzzleException $e) {
            dd($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['data'];
    }

    /**
     * @return array
     */
    public function getRoundForSeason(int $seasonId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY, 'include' => 'fixtures']
        ];

        try {
            $response = $client->request('GET', 'rounds/seasons/'.$seasonId, $options);
        } catch (GuzzleException $e) {
            dd($e);
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['data'];
    }

    /**
     * @return FixtureOddResponse[]
     */
    public function getOddsForFixture(int $fixtureId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY]
        ];

        try {
            $response = $client->request('GET', 'odds/fixture/'.$fixtureId, $options);
        } catch (GuzzleException) {
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (array_key_exists('error', $response)){
            if ($response['error']['code'] == 429){
                $this->logger->info("Limit reached for odds route");
                dd("Limit reached");
            }
        }

        return $this->parseOddResponse($response, $fixtureId);
    }

    public function getFixtureInfosByIds(array $fixtureIds)
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY, 'include' => 'scores;odds']
        ];

        try {
            $response = $client->request('GET', 'fixtures/multi/'.implode(',', $fixtureIds), $options);
        } catch (GuzzleException $e) {
            dd($e);
            return [];
        }

        $response = json_decode($response->getBody(), true);
        if (array_key_exists('error', $response)){
            if ($response['error']['code'] == 429){
                $this->logger->info("Limit reached for odds route");
                dd("Limit reached");
            }
        }

        return $response['data'];
        return $this->parseOddResponse($response, $fixtureIds);
    }

    /**
     * @return FixtureOddResponse[]
     */
    private function parseOddResponse(array $response, int $fixtureId): array
    {
        $oddResponses = [];
        if (empty($response['data'])){
            return [];
        }
        foreach ($response['data'] as $betVariant){
            $bookmakers = $betVariant['bookmaker']['data'];

            foreach ($bookmakers as $bookmaker){
                $oddResponse = new FixtureOddResponse();
                $oddResponse->setProvider($betVariant['name']);
                $oddResponse->setFixtureApiId($fixtureId);
                if ($betVariant['name'] === '3Way Result') {
                    $odds = $bookmaker['odds']['data'];
                    foreach ($odds as $odd) {
                        try {
                            $odd['value'] = str_replace(',', '', $odd['value']);
                            if ($odd['label'] == '1') {
                                $oddResponse->setHomeOdd($odd['value']);
                            }
                            if ($odd['label'] == 'X') {
                                $oddResponse->setDrawOdd($odd['value']);
                            }
                            if ($odd['label'] == '2') {
                                $oddResponse->setAwayOdd($odd['value']);
                            }
                        } catch (\Exception $e) {
                            dump($odd);
                            throw $e;
                        }
                    }
                    $oddResponse->setType(FixtureOdd::TYPE_CLASSIC);
                    $oddResponses[] = $oddResponse;

                }
                if ($betVariant['name'] === 'Double Chance') {
                    $odds = $bookmaker['odds']['data'];
                    foreach ($odds as $odd) {
                        try {
                            $odd['value'] = str_replace(',', '', $odd['value']);
                            if ($odd['label'] == '1X') {
                                $oddResponse->setHomeOdd($odd['value']);
                            }
                            if ($odd['label'] == '12') {
                                $oddResponse->setDrawOdd($odd['value']);
                            }
                            if ($odd['label'] == 'X2') {
                                $oddResponse->setAwayOdd($odd['value']);
                            }
                        } catch (\Exception $e) {
                            dump($odd);
                            throw $e;
                        }
                    }
                    $oddResponse->setType(FixtureOdd::TYPE_DOUBLE_CHANCE);
                    $oddResponses[] = $oddResponse;
                }
            }
        }
        return $oddResponses;
    }

    public function getStandingsForSeasonRound(int $seasonId, int $roundId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY]
        ];

        try {
            $response = $client->request('GET', 'standings/season/'.$seasonId.'/round/'.$roundId, $options);
        } catch (GuzzleException) {
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['data'];
    }

    /**
     * @param string|null $endDate
     * @return array
     */
    public function getFixturesInDateRangeForLeague(string $startDate, string $endDate, int $leagueId): array
    {
        $client = $this->clientFactory->createClient([], self::BASE_URI);

        $options = [
            'query' => ['api_token' => self::API_KEY, 'leagues' => $leagueId]
        ];

        try {
            $response = $client->request('GET', 'fixtures/between/'.$startDate.'/'.$endDate, $options);
        } catch (GuzzleException) {
            return [];
        }

        $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response['data'];
    }

    public function checkOddsAreAvailableForSeason(int $seasonId): bool
    {
        $seasons = $this->getRoundForSeason($seasonId);

        return $seasons[0]['fixtures'][0]['has_odds'];
    }


    // TODO
    // 1.get rounds ?
    // 2.get fixtures for rounds </
    // 3.get odds for fixtures </
    // 4.get standings
    // 5.calculate seedings
    // search league by name

}
