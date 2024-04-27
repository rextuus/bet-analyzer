<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\Round\SpmRoundRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmRoundRepository::class)]
class SpmRound
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $leagueApiId = null;

    #[ORM\Column]
    private ?int $seasonApiId = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startingAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endingAt = null;

    #[ORM\Column]
    private ?bool $fixturesComplete = null;

    #[ORM\Column]
    private ?bool $oddsComplete = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSeasonApiId(): ?int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): static
    {
        $this->seasonApiId = $seasonApiId;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function isFixturesComplete(): ?bool
    {
        return $this->fixturesComplete;
    }

    public function setFixturesComplete(bool $fixturesComplete): static
    {
        $this->fixturesComplete = $fixturesComplete;

        return $this;
    }

    public function isOddsComplete(): ?bool
    {
        return $this->oddsComplete;
    }

    public function setOddsComplete(bool $oddsComplete): static
    {
        $this->oddsComplete = $oddsComplete;

        return $this;
    }
}
