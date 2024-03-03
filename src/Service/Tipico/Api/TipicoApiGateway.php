<?php
declare(strict_types=1);

namespace App\Service\Tipico\Api;

use App\Service\Sportmonks\Api\GuzzleClientFactory;
use App\Service\Tipico\Api\Response\TipicoDailyMatchesResponse;
use App\Service\Tipico\Api\Response\TipicoMatchResultResponse;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoApiGateway
{
  const DAILY_MATCH_API_URI = 'https://sports.tipico.de/json/program/hourEvents/today';
  const EVENT_INFO_API_URI = 'https://sports.tipico.de/json/services/event/598504610';


    public function __construct(
        private readonly GuzzleClientFactory $clientFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getDailyMatchEvents(): TipicoDailyMatchesResponse
    {
        $client = $this->clientFactory->createClient([], self::DAILY_MATCH_API_URI);

        $response = $client->request('GET');

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $response = new TipicoDailyMatchesResponse($data);
        $response->parseResponse();

        return $response;
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
