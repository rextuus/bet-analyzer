<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Round\Data;

use App\Service\Sportmonks\Content\League\Data\SpmLeagueData;
use DateTime;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmRoundData
{
    private int $leagueApiId;
    private int $seasonApiId;
    private int $apiId;
    private string $name;
    private DateTime $startingAt;
    private DateTime $endingAt;
    private bool $oddsCompleted;
    private bool $fixtureCompleted;

    public function getLeagueApiId(): int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): SpmRoundData
    {
        $this->leagueApiId = $leagueApiId;
        return $this;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): SpmRoundData
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmRoundData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SpmRoundData
    {
        $this->name = $name;
        return $this;
    }

    public function getStartingAt(): DateTime
    {
        return $this->startingAt;
    }

    public function setStartingAt(DateTime $startingAt): SpmRoundData
    {
        $this->startingAt = $startingAt;
        return $this;
    }

    public function getEndingAt(): DateTime
    {
        return $this->endingAt;
    }

    public function setEndingAt(DateTime $endingAt): SpmRoundData
    {
        $this->endingAt = $endingAt;
        return $this;
    }

    public function isOddsCompleted(): bool
    {
        return $this->oddsCompleted;
    }

    public function setOddsCompleted(bool $oddsCompleted): SpmRoundData
    {
        $this->oddsCompleted = $oddsCompleted;
        return $this;
    }

    public function isFixtureCompleted(): bool
    {
        return $this->fixtureCompleted;
    }

    public function setFixtureCompleted(bool $fixtureCompleted): SpmRoundData
    {
        $this->fixtureCompleted = $fixtureCompleted;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmRoundData
    {
        $needed = ['id', 'name', 'league_id', 'season_id', 'starting_at', 'ending_at'];
        $this->checkForNecessaryKeys($needed, $response);

        $startTime = new DateTime('01-01-2000');
        if ($response['starting_at']){
            $startTime = new DateTime($response['starting_at']);
        }

        $endTime = new DateTime('01-01-2000');
        if ($response['ending_at']){
            $endTime = new DateTime($response['ending_at']);
        }


        $this->setApiId($response['id']);
        $this->setName($response['name']);
        $this->setLeagueApiId($response['league_id']);
        $this->setSeasonApiId($response['season_id']);
        $this->setStartingAt($startTime);
        $this->setEndingAt($endTime);
        $this->setFixtureCompleted(false);
        $this->setOddsCompleted(false);
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
