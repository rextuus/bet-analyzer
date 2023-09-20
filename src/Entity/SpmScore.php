<?php

namespace App\Entity;

use App\Service\Sportmonks\Content\Score\SpmScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmScoreRepository::class)]
class SpmScore
{
    public const FIRST_HALF = '1ST_HALF';
    public const SECOND_HALF = '2ND_HALF';
    public const PARTICIPANT_HOME = 'home';
    public const PARTICIPANT_AWAY = 'away';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    #[ORM\Column]
    private ?int $fixtureApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $goals = null;

    #[ORM\Column]
    private ?int $participantApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $participant = null;

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

    public function getFixtureApiId(): ?int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): static
    {
        $this->fixtureApiId = $fixtureApiId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGoals(): ?int
    {
        return $this->goals;
    }

    public function setGoals(int $goals): static
    {
        $this->goals = $goals;

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

    public function getParticipant(): ?string
    {
        return $this->participant;
    }

    public function setParticipant(string $participant): static
    {
        $this->participant = $participant;

        return $this;
    }
}
