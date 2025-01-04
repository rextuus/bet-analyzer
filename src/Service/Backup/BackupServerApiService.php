<?php

declare(strict_types=1);

namespace App\Service\Backup;

use DateTime;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BackupServerApiService
{
    public function __construct(#[Autowire(env: 'string:BACKUP_SERVER_IP')] private readonly string $serverIp)
    {
    }

    public function getBetsForTimeInterval(DateTime $from, DateTime $until): ResponseInterface
    {
        $fromDateTimeStamp = $from->getTimestamp() * 1000;
        $untilDateTimeStamp = $until->getTimestamp() * 1000;

        $httpClient = HttpClient::create();

        return $httpClient->request(
            'GET',
            "http://$this->serverIp/api/bets?from=$fromDateTimeStamp&until=$untilDateTimeStamp"
        );
    }
}
