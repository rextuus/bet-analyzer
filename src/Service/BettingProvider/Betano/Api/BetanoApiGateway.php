<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Api;

use App\Service\BettingProvider\Betano\Api\Response\DailyMatchEventResponse;
use App\Service\Sportmonks\Api\GuzzleClientFactory;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoApiGateway
{
    private const DAILY_MATCH_API_URI = 'https://www.betano.de/api/sport/fussball/anstehende-spiele-heute/?sort=StartTime';
    private const SEARCH_API_URI = 'https://www.betano.de/api/search/?term=HB%20Torshavn%20-%20Eb/Streymur';
    private const BET = 'https://sports.bwin.de/cds-api/bettingoffer/fixtures?x-bwin-accessid=NWQyNmIwMjUtZDQ3NC00NDQxLWI5YTktNjdkYjZjOTg1OWEz&lang=de&country=DE&userCountry=DE&fixtureTypes=Standard&state=Latest&offerMapping=Filtered&offerCategories=Gridable&fixtureCategories=Gridable,NonGridable,Other,Specials,Outrights&sportIds=4&regionIds=&competitionIds=&conferenceIds=&isPriceBoost=false&statisticsModes=None&skip=0&take=500&sortBy=StartDate&from=2024-04-26T00:00:00.000Z&to=2024-04-26T23:59:00.000Z';

    public function __construct(private readonly GuzzleClientFactory $clientFactory)
    {
    }

    public function getNextDailyMatchEvents(): DailyMatchEventResponse
    {
        $header = [
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0',
            'Accept' => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            'Accept-Encoding' => "gzip, deflate, br",
            "Accept-Language" => "de,en-US;q=0.7,en;q=0.3",
            "Cache-Control" => "no-cache",
            "Connection" => "keep-alive",
            "DNT" => "1",
            "Host" => "www.betano.de",
            "Pragma" => "no-cache",
            "Sec-Fetch-Dest" => "document",
            "Sec-Fetch-Mode" => "navigate",
            "Sec-Fetch-Site" => "none",
            "Sec-Fetch-User" => "?1",
            "Sec-GPC" => "1",
            "TE" => "trailers",
            "Upgrade-Insecure-Requests" => "1",
        ];
        $client = $this->clientFactory->createClient($header, self::BET);

        $response = $client->request('GET');
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        dd($data);
        $response = new DailyMatchEventResponse($data);
        $response->parseResponse();

        return $response;
    }
}
