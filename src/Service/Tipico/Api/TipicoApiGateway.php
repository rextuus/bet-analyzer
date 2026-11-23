<?php

declare(strict_types=1);

namespace App\Service\Tipico\Api;

use App\Service\Http\GuzzleClientFactory;
use App\Service\Tipico\Api\Response\TipicoDailyMatchesResponse;
use App\Service\Tipico\Api\Response\TipicoMatchResultResponse;
use GuzzleHttp\Exception\GuzzleException;


class TipicoApiGateway
{
    // Seit Ende 11/2025 leitet /json/program/hourEvents/today hierher um; ohne maxMarkets antwortet das Gateway
    // mit 400. Erlaubt sind nur bestimmte Werte (1 und 5 geprüft am 20.11.2026, 3/6/8/10 → 400). Mit 5 kommen
    // standard, points-more-less-than und score-both mit – head-to-head bietet Tipico dort nicht mehr an.
    const DAILY_MATCH_API_URI = 'https://sports.tipico.de/v1/tpapi/programgateway/program/events/hourEvents/today?maxMarkets=5&language=de';
    // Der letzte Pfadteil wird durch die Tipico-ID ersetzt (relative Auflösung in Guzzle).
    const EVENT_INFO_API_URI = 'https://sports.tipico.de/json/services/event/0';

    public function __construct(
        private readonly GuzzleClientFactory $clientFactory
    ) {
    }

    public function getDailyMatchEvents(): TipicoDailyMatchesResponse
    {
        $data = json_decode($this->getDailyMatchEventsRaw(), true, 512, JSON_THROW_ON_ERROR);
        $response = new TipicoDailyMatchesResponse($data);
        $response->parseResponse();

        return $response;
    }

    public function getDailyMatchEventsRaw(): string
    {
        $client = $this->clientFactory->createClient([], self::DAILY_MATCH_API_URI);

        $response = $client->request('GET');

        return $response->getBody()->getContents();
    }

    public function getEventInfo(string $tipicoMatchId): ?TipicoMatchResultResponse
    {
        $client = $this->clientFactory->createClient([], self::EVENT_INFO_API_URI);

        try {
            $response = $client->request('GET', $tipicoMatchId);
        } catch (GuzzleException $e) {
            return null;
        }

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $response = new TipicoMatchResultResponse($data);
        $response->parseResponse();

        return $response;
    }
}
