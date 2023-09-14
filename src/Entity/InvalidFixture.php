<?php

namespace App\Entity;

use App\Service\Sportmonks\Content\Fixture\InvalidFixture\InvalidFixtureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvalidFixtureRepository::class)]
class InvalidFixture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fixtureApiId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $decorationAttempt = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDecorationAttempt(): ?\DateTimeInterface
    {
        return $this->decorationAttempt;
    }

    public function setDecorationAttempt(\DateTimeInterface $decorationAttempt): static
    {
        $this->decorationAttempt = $decorationAttempt;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
}
