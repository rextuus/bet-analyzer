<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\League\Data;


class SpmLeagueData
{
    private int $country;
    private string $short;
    private string $name;
    private int $apiId;

    public function getCountry(): int
    {
        return $this->country;
    }

    public function setCountry(int $country): SpmLeagueData
    {
        $this->country = $country;
        return $this;
    }

    public function getShort(): string
    {
        return $this->short;
    }

    public function setShort(string $short): SpmLeagueData
    {
        $this->short = $short;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SpmLeagueData
    {
        $this->name = $name;
        return $this;
    }

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmLeagueData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmLeagueData
    {
        $needed = ['country_id', 'name', 'short_code', 'id'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setName($response['name']);
        $this->setShort($response['short_code']);
        $this->setCountry($response['country_id']);
        return $this;
    }

    private function checkForNecessaryKeys(array $keys, array $response): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $response)) {
                throw new \Exception(sprintf('Expected api response key %s for class %s', $key, get_class($this)));
            }
        }
    }
}
