<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture\Data;

use App\Entity\SpmFixture;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;
use DateTime;
use DateTimeInterface;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmFixtureData
{
    private int $apiId;
    private int $leagueApiId;
    private int $seasonApiId;
    private int $roundApiId;
    private DateTimeInterface $startingAt;
    private int $startingAtTimestamp;
    private string $resultInfo;
    private bool $hasOdds;
    private bool $oddDecorated;
    private bool $scoreDecorated;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmFixtureData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getLeagueApiId(): int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): SpmFixtureData
    {
        $this->leagueApiId = $leagueApiId;
        return $this;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): SpmFixtureData
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function getRoundApiId(): int
    {
        return $this->roundApiId;
    }

    public function setRoundApiId(int $roundApiId): SpmFixtureData
    {
        $this->roundApiId = $roundApiId;
        return $this;
    }

    public function getStartingAt(): DateTime
    {
        return $this->startingAt;
    }

    public function setStartingAt(DateTimeInterface $startingAt): SpmFixtureData
    {
        $this->startingAt = $startingAt;
        return $this;
    }

    public function getStartingAtTimestamp(): int
    {
        return $this->startingAtTimestamp;
    }

    public function setStartingAtTimestamp(int $startingAtTimestamp): SpmFixtureData
    {
        $this->startingAtTimestamp = $startingAtTimestamp;
        return $this;
    }

    public function getResultInfo(): string
    {
        return $this->resultInfo;
    }

    public function setResultInfo(string $resultInfo): SpmFixtureData
    {
        $this->resultInfo = $resultInfo;
        return $this;
    }

    public function isHasOdds(): bool
    {
        return $this->hasOdds;
    }

    public function setHasOdds(bool $hasOdds): SpmFixtureData
    {
        $this->hasOdds = $hasOdds;
        return $this;
    }

    public function isOddDecorated(): bool
    {
        return $this->oddDecorated;
    }

    public function setOddDecorated(bool $oddDecorated): SpmFixtureData
    {
        $this->oddDecorated = $oddDecorated;
        return $this;
    }

    public function isScoreDecorated(): bool
    {
        return $this->scoreDecorated;
    }

    public function setScoreDecorated(bool $scoreDecorated): SpmFixtureData
    {
        $this->scoreDecorated = $scoreDecorated;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmFixtureData
    {
        $needed = ['id', 'season_id', 'league_id', 'result_info', 'starting_at', 'starting_at_timestamp', 'has_odds', 'round_id'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setSeasonApiId($response['season_id']);
        $this->setRoundApiId($response['round_id']);
        $this->setLeagueApiId($response['league_id']);
        $this->setResultInfo($response['result_info'] ?: '');
        $this->setStartingAt(new DateTime($response['starting_at']));
        $this->setStartingAtTimestamp($response['starting_at_timestamp']);
        $this->setHasOdds($response['has_odds']);
        $this->setOddDecorated(false);
        $this->setScoreDecorated(false);
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

    public function initFromEntity(SpmFixture $fixture): SpmFixtureData
    {
        $this->setApiId($fixture->getApiId());
        $this->setSeasonApiId($fixture->getSeasonApiId());
        $this->setRoundApiId($fixture->getRoundApiId());
        $this->setLeagueApiId($fixture->getLeagueApiId());
        $this->setResultInfo($fixture->getResultInfo());
        $this->setStartingAt($fixture->getStartingAt());
        $this->setStartingAtTimestamp($fixture->getStartingAtTimestamp());
        $this->setOddDecorated($fixture->isOddDecorated());
        $this->setScoreDecorated($fixture->isScoreDecorated());
        return $this;
    }
}
