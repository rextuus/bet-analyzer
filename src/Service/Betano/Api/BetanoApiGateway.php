<?php

declare(strict_types=1);

namespace App\Service\Betano\Api;

use App\Service\Betano\Api\Response\DailyMatchEventResponse;
use App\Service\Sportmonks\Api\GuzzleClientFactory;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoApiGateway
{
    private const DAILY_MATCH_API_URI = 'https://www.betano.de/api/sport/fussball/anstehende-spiele-heute/?sort=StartTime';

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
        $client = $this->clientFactory->createClient($header, self::DAILY_MATCH_API_URI);

        $response = $client->request('GET');
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $response = new DailyMatchEventResponse($data);
        $response->parseResponse();

        return $response;
    }
}
