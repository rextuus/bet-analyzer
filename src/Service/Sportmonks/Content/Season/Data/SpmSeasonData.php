<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season\Data;

use App\Entity\Spm\SpmSeason;
use DateTime;
use DateTimeInterface;


class SpmSeasonData
{
    private int $apiId;
    private int $leagueApiId;
    private string $name;
    private bool $finished;
    private bool $isCurrent;
    private DateTimeInterface $startingAt;
    private DateTimeInterface $endingAt;
    private int $fixtureDecorated;
    private int $oddDecorated;
    private int $expectedFixtures;
    private ?string $displayName;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmSeasonData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getLeagueApiId(): int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): SpmSeasonData
    {
        $this->leagueApiId = $leagueApiId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SpmSeasonData
    {
        $this->name = $name;
        return $this;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): SpmSeasonData
    {
        $this->finished = $finished;
        return $this;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(bool $isCurrent): SpmSeasonData
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    public function getStartingAt(): DateTimeInterface
    {
        return $this->startingAt;
    }

    public function setStartingAt(DateTimeInterface $startingAt): SpmSeasonData
    {
        $this->startingAt = $startingAt;
        return $this;
    }

    public function getEndingAt(): DateTimeInterface
    {
        return $this->endingAt;
    }

    public function setEndingAt(DateTimeInterface $endingAt): SpmSeasonData
    {
        $this->endingAt = $endingAt;
        return $this;
    }

    public function getFixtureDecorated(): int
    {
        return $this->fixtureDecorated;
    }

    public function setFixtureDecorated(int $fixtureDecorated): SpmSeasonData
    {
        $this->fixtureDecorated = $fixtureDecorated;
        return $this;
    }

    public function getOddDecorated(): int
    {
        return $this->oddDecorated;
    }

    public function setOddDecorated(int $oddDecorated): SpmSeasonData
    {
        $this->oddDecorated = $oddDecorated;
        return $this;
    }

    public function getExpectedFixtures(): int
    {
        return $this->expectedFixtures;
    }

    public function setExpectedFixtures(int $expectedFixtures): SpmSeasonData
    {
        $this->expectedFixtures = $expectedFixtures;
        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): SpmSeasonData
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmSeasonData
    {
        $needed = ['id', 'league_id', 'name', 'finished', 'is_current', 'starting_at', 'ending_at'];
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
        $this->setLeagueApiId($response['league_id']);
        $this->setName($response['name']);
        $this->setFinished($response['finished']);
        $this->setIsCurrent($response['is_current']);
        $this->setStartingAt($startTime);
        $this->setEndingAt($endTime);
        $this->setFixtureDecorated(0);
        $this->setOddDecorated(0);
        $this->setExpectedFixtures(0);

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

    public function initFromEntity(SpmSeason $season): SpmSeasonData
    {
        $this->setApiId($season->getApiId());
        $this->setLeagueApiId($season->getLeagueApiId());
        $this->setName($season->getName());
        $this->setFinished($season->isFinished());
        $this->setIsCurrent($season->isIsCurrent());
        $this->setStartingAt($season->getStartingAt());
        $this->setEndingAt($season->getEndingAt());
        $this->setFixtureDecorated($season->getFixtureDecorated());
        $this->setOddDecorated($season->getOddDecorated());
        $this->setExpectedFixtures($season->getExpectedFixtures());
        $this->setDisplayName($season->getDisplayName());

        return $this;
    }
}
