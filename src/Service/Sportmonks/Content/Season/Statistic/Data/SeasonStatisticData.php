<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season\Statistic\Data;

use App\Entity\Spm\SeasonStatistic;


class SeasonStatisticData
{
    private int $seasonApiId;
    private string $league;
    private string $year;
    private int $matchDays;
    private int $teams;
    private int $decoratedFixtures;
    private string $stage;
    private bool $isReliable;
    private bool $isFaulty;
    private bool $isRegularSeason;
    private bool $noStandingsAvailable;
    private int $expectedMatchDays;
    private int $expectedMatchDaysAlternative;
    private int $actuallyBetDecorated;
    private bool $manuallyConfirmed;

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): SeasonStatisticData
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function getLeague(): string
    {
        return $this->league;
    }

    public function setLeague(string $league): SeasonStatisticData
    {
        $this->league = $league;
        return $this;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): SeasonStatisticData
    {
        $this->year = $year;
        return $this;
    }

    public function getMatchDays(): int
    {
        return $this->matchDays;
    }

    public function setMatchDays(int $matchDays): SeasonStatisticData
    {
        $this->matchDays = $matchDays;
        return $this;
    }

    public function getTeams(): int
    {
        return $this->teams;
    }

    public function setTeams(int $teams): SeasonStatisticData
    {
        $this->teams = $teams;
        return $this;
    }

    public function getDecoratedFixtures(): int
    {
        return $this->decoratedFixtures;
    }

    public function setDecoratedFixtures(int $decoratedFixtures): SeasonStatisticData
    {
        $this->decoratedFixtures = $decoratedFixtures;
        return $this;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function setStage(string $stage): SeasonStatisticData
    {
        $this->stage = $stage;
        return $this;
    }

    public function isReliable(): bool
    {
        return $this->isReliable;
    }

    public function setIsReliable(bool $isReliable): SeasonStatisticData
    {
        $this->isReliable = $isReliable;
        return $this;
    }

    public function isFaulty(): bool
    {
        return $this->isFaulty;
    }

    public function setIsFaulty(bool $isFaulty): SeasonStatisticData
    {
        $this->isFaulty = $isFaulty;
        return $this;
    }

    public function isRegularSeason(): bool
    {
        return $this->isRegularSeason;
    }

    public function setIsRegularSeason(bool $isRegularSeason): SeasonStatisticData
    {
        $this->isRegularSeason = $isRegularSeason;
        return $this;
    }

    public function getExpectedMatchDays(): int
    {
        return $this->expectedMatchDays;
    }

    public function setExpectedMatchDays(int $expectedMatchDays): SeasonStatisticData
    {
        $this->expectedMatchDays = $expectedMatchDays;
        return $this;
    }

    public function getExpectedMatchDaysAlternative(): int
    {
        return $this->expectedMatchDaysAlternative;
    }

    public function setExpectedMatchDaysAlternative(int $expectedMatchDaysAlternative): SeasonStatisticData
    {
        $this->expectedMatchDaysAlternative = $expectedMatchDaysAlternative;
        return $this;
    }

    public function isNoStandingsAvailable(): bool
    {
        return $this->noStandingsAvailable;
    }

    public function setNoStandingsAvailable(bool $noStandingsAvailable): SeasonStatisticData
    {
        $this->noStandingsAvailable = $noStandingsAvailable;
        return $this;
    }

    public function getActuallyBetDecorated(): int
    {
        return $this->actuallyBetDecorated;
    }

    public function setActuallyBetDecorated(int $actuallyBetDecorated): SeasonStatisticData
    {
        $this->actuallyBetDecorated = $actuallyBetDecorated;
        return $this;
    }

    public function isManuallyConfirmed(): bool
    {
        return $this->manuallyConfirmed;
    }

    public function setManuallyConfirmed(bool $manuallyConfirmed): SeasonStatisticData
    {
        $this->manuallyConfirmed = $manuallyConfirmed;
        return $this;
    }

    public function initFromEntity(SeasonStatistic $statistic): SeasonStatisticData
    {
        $this->setActuallyBetDecorated($statistic->getActuallyBetDecorated());
        $this->setManuallyConfirmed($statistic->isManuallyConfirmed());
        $this->setTeams($statistic->getTeams());
        $this->setStage($statistic->getStage());
        $this->setYear($statistic->getYear());
        $this->setLeague($statistic->getLeague());
        $this->setExpectedMatchDaysAlternative($statistic->getExpectedMatchDaysAlternative());
        $this->setExpectedMatchDays($statistic->getExpectedMatchDays());
        $this->setIsFaulty($statistic->isIsFaulty());
        $this->setIsReliable($statistic->isIsReliable());
        $this->setSeasonApiId($statistic->getSeasonApiId());
        $this->setIsRegularSeason($statistic->isIsRegularSeason());
        $this->setDecoratedFixtures($statistic->getDecoratedFixtures());
        $this->setMatchDays($statistic->getExpectedMatchDays());
        $this->setNoStandingsAvailable($statistic->isNoStandingsAvailable());
        return $this;
    }
}
