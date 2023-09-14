<?php

namespace App\Entity;

use App\Service\Sportmonks\Content\Season\SpmSeasonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmSeasonRepository::class)]
class SpmSeason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    #[ORM\Column]
    private ?int $leagueApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $finished = null;

    #[ORM\Column]
    private ?bool $isCurrent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startingAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endingAt = null;

    #[ORM\Column]
    private ?int $fixtureDecorated = null;

    #[ORM\Column]
    private ?int $oddDecorated = null;

    #[ORM\Column]
    private ?int $expectedFixtures = null;

    #[ORM\Column(nullable: true)]
    private ?bool $standingsAvailable = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiId(): ?int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): static
    {
        $this->apiId = $apiId;

        return $this;
    }

    public function getLeagueApiId(): ?int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): static
    {
        $this->leagueApiId = $leagueApiId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): static
    {
        $this->finished = $finished;

        return $this;
    }

    public function isIsCurrent(): ?bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(bool $isCurrent): static
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }

    public function getStartingAt(): ?\DateTimeInterface
    {
        return $this->startingAt;
    }

    public function setStartingAt(\DateTimeInterface $startingAt): static
    {
        $this->startingAt = $startingAt;

        return $this;
    }

    public function getEndingAt(): ?\DateTimeInterface
    {
        return $this->endingAt;
    }

    public function setEndingAt(\DateTimeInterface $endingAt): static
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    public function getFixtureDecorated(): ?int
    {
        return $this->fixtureDecorated;
    }

    public function setFixtureDecorated(int $fixtureDecorated): static
    {
        $this->fixtureDecorated = $fixtureDecorated;

        return $this;
    }

    public function getOddDecorated(): ?int
    {
        return $this->oddDecorated;
    }

    public function setOddDecorated(int $oddDecorated): static
    {
        $this->oddDecorated = $oddDecorated;

        return $this;
    }

    public function getExpectedFixtures(): ?int
    {
        return $this->expectedFixtures;
    }

    public function setExpectedFixtures(int $expectedFixtures): static
    {
        $this->expectedFixtures = $expectedFixtures;

        return $this;
    }

    public function isStandingsAvailable(): ?bool
    {
        return $this->standingsAvailable;
    }

    public function setStandingsAvailable(bool $standingsAvailable): static
    {
        $this->standingsAvailable = $standingsAvailable;

        return $this;
    }
}
