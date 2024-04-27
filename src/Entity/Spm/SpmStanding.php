<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\Standing\SpmStandingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmStandingRepository::class)]
class SpmStanding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    #[ORM\Column]
    private ?int $participantApiId = null;

    #[ORM\Column]
    private ?int $leagueApiId = null;

    #[ORM\Column]
    private ?int $seasonApiId = null;

    #[ORM\Column]
    private ?int $roundApiId = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column]
    private ?int $stageApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

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

    public function getParticipantApiId(): ?int
    {
        return $this->participantApiId;
    }

    public function setParticipantApiId(int $participantApiId): static
    {
        $this->participantApiId = $participantApiId;

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

    public function getSeasonApiId(): ?int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): static
    {
        $this->seasonApiId = $seasonApiId;

        return $this;
    }

    public function getRoundApiId(): ?int
    {
        return $this->roundApiId;
    }

    public function setRoundApiId(int $roundApiId): static
    {
        $this->roundApiId = $roundApiId;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getStageApiId(): ?int
    {
        return $this->stageApiId;
    }

    public function setStageApiId(int $stageApiId): static
    {
        $this->stageApiId = $stageApiId;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }
}
