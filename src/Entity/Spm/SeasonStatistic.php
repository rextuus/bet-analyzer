<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonStatisticRepository::class)]
class SeasonStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $seasonApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $league = null;

    #[ORM\Column(length: 255)]
    private ?string $year = null;

    #[ORM\Column]
    private ?int $matchDays = null;

    #[ORM\Column]
    private ?int $teams = null;

    #[ORM\Column]
    private ?int $decoratedFixtures = null;

    #[ORM\Column(length: 255)]
    private ?string $stage = null;

    #[ORM\Column]
    private ?bool $isReliable = false;

    #[ORM\Column]
    private ?bool $isFaulty = false;

    #[ORM\Column]
    private ?bool $isRegularSeason = true;

    #[ORM\Column]
    private ?int $expectedMatchDays = null;

    #[ORM\Column]
    private ?int $expectedMatchDaysAlternative = null;

    #[ORM\Column]
    private ?bool $noStandingsAvailable = false;

    #[ORM\Column]
    private ?bool $manuallyConfirmed = null;

    #[ORM\Column]
    private ?int $actuallyBetDecorated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeasonApiId(): ?int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): static
    {
        $this->seasonApiId = $seasonApiId;

        return $this;
    }

    public function getLeague(): ?string
    {
        return $this->league;
    }

    public function setLeague(string $league): static
    {
        $this->league = $league;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getMatchDays(): ?int
    {
        return $this->matchDays;
    }

    public function setMatchDays(int $matchDays): static
    {
        $this->matchDays = $matchDays;

        return $this;
    }

    public function getTeams(): ?int
    {
        return $this->teams;
    }

    public function setTeams(int $teams): static
    {
        $this->teams = $teams;

        return $this;
    }

    public function getDecoratedFixtures(): ?int
    {
        return $this->decoratedFixtures;
    }

    public function setDecoratedFixtures(int $decoratedFixtures): static
    {
        $this->decoratedFixtures = $decoratedFixtures;

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function isIsReliable(): ?bool
    {
        return $this->isReliable;
    }

    public function setIsReliable(bool $isReliable): static
    {
        $this->isReliable = $isReliable;

        return $this;
    }

    public function isIsFaulty(): ?bool
    {
        return $this->isFaulty;
    }

    public function setIsFaulty(bool $isFaulty): static
    {
        $this->isFaulty = $isFaulty;

        return $this;
    }

    public function isIsRegularSeason(): ?bool
    {
        return $this->isRegularSeason;
    }

    public function setIsRegularSeason(bool $isRegularSeason): static
    {
        $this->isRegularSeason = $isRegularSeason;

        return $this;
    }

    public function getExpectedMatchDays(): ?int
    {
        return $this->expectedMatchDays;
    }

    public function setExpectedMatchDays(int $expectedMatchDays): static
    {
        $this->expectedMatchDays = $expectedMatchDays;

        return $this;
    }

    public function getExpectedMatchDaysAlternative(): ?int
    {
        return $this->expectedMatchDaysAlternative;
    }

    public function setExpectedMatchDaysAlternative(int $expectedMatchDaysAlternative): static
    {
        $this->expectedMatchDaysAlternative = $expectedMatchDaysAlternative;

        return $this;
    }

    public function isNoStandingsAvailable(): ?bool
    {
        return $this->noStandingsAvailable;
    }

    public function setNoStandingsAvailable(bool $noStandingsAvailable): static
    {
        $this->noStandingsAvailable = $noStandingsAvailable;

        return $this;
    }

    public function isManuallyConfirmed(): ?bool
    {
        return $this->manuallyConfirmed;
    }

    public function setManuallyConfirmed(bool $manuallyConfirmed): static
    {
        $this->manuallyConfirmed = $manuallyConfirmed;

        return $this;
    }

    public function getActuallyBetDecorated(): ?int
    {
        return $this->actuallyBetDecorated;
    }

    public function setActuallyBetDecorated(int $actuallyBetDecorated): static
    {
        $this->actuallyBetDecorated = $actuallyBetDecorated;

        return $this;
    }
}
