<?php


namespace App\Service\Sportmonks\Api;


use GuzzleHttp\Client;

class GuzzleClientFactory
{
    /**
     *
     * @return Client
     */
    public function createClient(array $headers, string $baseUri){
        return new Client(['base_uri' => $baseUri, 'headers' => $headers]);
    }
}
