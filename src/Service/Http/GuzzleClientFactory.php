<?php

declare(strict_types=1);

namespace App\Service\Http;

use GuzzleHttp\Client;

class GuzzleClientFactory
{
    // Ohne Timeout hängt ein Cronjob im Zweifel ewig an einer toten Gegenstelle.
    private const TIMEOUT_SECONDS = 30;

    public function createClient(array $headers, string $baseUri): Client
    {
        return new Client([
            'base_uri' => $baseUri,
            'headers' => $headers,
            'timeout' => self::TIMEOUT_SECONDS,
            'connect_timeout' => 10,
        ]);
    }
}
