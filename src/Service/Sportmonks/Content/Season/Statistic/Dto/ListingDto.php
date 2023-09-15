<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season\Statistic\Dto;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class ListingDto
{
    private string $league;
    private string $year;
    private int $matchDays;
    private int $teams;
    private int $expectedMatchDays;
    private int $expectedMatchDaysAlternative;
    private int $decoratedFixtures;
    private string $stage;
    private string $searchLinkContent;
    private bool $noStandings;
    private bool $manuallyConfirmed;
    private int $actuallyBetDecorated;
    private int $seasonId;

    // css
    private string $invalidStageClass = '';
    private string $invalidExpectationsClass = '';
    private string $fitsExpectationClass = '';
    private string $invalidTeamAmount = '';
    private string $confirmedManuallyClass = '';

    public function getLeague(): string
    {
        return $this->league;
    }

    public function setLeague(string $league): ListingDto
    {
        $this->league = $league;
        return $this;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): ListingDto
    {
        $this->year = $year;
        return $this;
    }

    public function getMatchDays(): int
    {
        return $this->matchDays;
    }

    public function setMatchDays(int $matchDays): ListingDto
    {
        $this->matchDays = $matchDays;
        return $this;
    }

    public function getTeams(): int
    {
        return $this->teams;
    }

    public function setTeams(int $teams): ListingDto
    {
        $this->teams = $teams;
        return $this;
    }

    public function getExpectedMatchDays(): int
    {
        return $this->expectedMatchDays;
    }

    public function setExpectedMatchDays(int $expectedMatchDays): ListingDto
    {
        $this->expectedMatchDays = $expectedMatchDays;
        return $this;
    }

    public function getExpectedMatchDaysAlternative(): int
    {
        return $this->expectedMatchDaysAlternative;
    }

    public function setExpectedMatchDaysAlternative(int $expectedMatchDaysAlternative): ListingDto
    {
        $this->expectedMatchDaysAlternative = $expectedMatchDaysAlternative;
        return $this;
    }

    public function getDecoratedFixtures(): int
    {
        return $this->decoratedFixtures;
    }

    public function setDecoratedFixtures(int $decoratedFixtures): ListingDto
    {
        $this->decoratedFixtures = $decoratedFixtures;
        return $this;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function setStage(string $stage): ListingDto
    {
        $this->stage = $stage;
        return $this;
    }

    public function getInvalidStageClass(): string
    {
        return $this->invalidStageClass;
    }

    public function setInvalidStageClass(string $invalidStageClass): ListingDto
    {
        $this->invalidStageClass = $invalidStageClass;
        return $this;
    }

    public function getInvalidExpectationsClass(): string
    {
        return $this->invalidExpectationsClass;
    }

    public function setInvalidExpectationsClass(string $invalidExpectationsClass): ListingDto
    {
        $this->invalidExpectationsClass = $invalidExpectationsClass;
        return $this;
    }

    public function getFitsExpectationClass(): string
    {
        return $this->fitsExpectationClass;
    }

    public function setFitsExpectationClass(string $fitsExpectationClass): ListingDto
    {
        $this->fitsExpectationClass = $fitsExpectationClass;
        return $this;
    }

    public function isNoStandings(): bool
    {
        return $this->noStandings;
    }

    public function setNoStandings(bool $noStandings): ListingDto
    {
        $this->noStandings = $noStandings;
        return $this;
    }

    public function getInvalidTeamAmount(): string
    {
        return $this->invalidTeamAmount;
    }

    public function setInvalidTeamAmount(string $invalidTeamAmount): ListingDto
    {
        $this->invalidTeamAmount = $invalidTeamAmount;
        return $this;
    }

    public function getSearchLinkContent(): string
    {
        return $this->searchLinkContent;
    }

    public function setSearchLinkContent(string $searchLinkContent): ListingDto
    {
        $this->searchLinkContent = $searchLinkContent;
        return $this;
    }

    public function isManuallyConfirmed(): bool
    {
        return $this->manuallyConfirmed;
    }

    public function setManuallyConfirmed(bool $manuallyConfirmed): ListingDto
    {
        $this->manuallyConfirmed = $manuallyConfirmed;
        return $this;
    }

    public function getActuallyBetDecorated(): int
    {
        return $this->actuallyBetDecorated;
    }

    public function setActuallyBetDecorated(int $actuallyBetDecorated): ListingDto
    {
        $this->actuallyBetDecorated = $actuallyBetDecorated;
        return $this;
    }

    public function getSeasonId(): int
    {
        return $this->seasonId;
    }

    public function setSeasonId(int $seasonId): ListingDto
    {
        $this->seasonId = $seasonId;
        return $this;
    }

    public function getConfirmedManuallyClass(): string
    {
        return $this->confirmedManuallyClass;
    }

    public function setConfirmedManuallyClass(string $confirmedManuallyClass): ListingDto
    {
        $this->confirmedManuallyClass = $confirmedManuallyClass;
        return $this;
    }
}
