<?php

namespace App\Entity;

use App\Service\Betano\Content\BetanoSettings\BetanoSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetanoSettingsRepository::class)]
class BetanoSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $collectionEnabled = null;

    #[ORM\Column]
    private ?bool $collectionRunning = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $expectedExecutionTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCollectionEnabled(): ?bool
    {
        return $this->collectionEnabled;
    }

    public function setCollectionEnabled(bool $collectionEnabled): static
    {
        $this->collectionEnabled = $collectionEnabled;

        return $this;
    }

    public function isCollectionRunning(): ?bool
    {
        return $this->collectionRunning;
    }

    public function setCollectionRunning(bool $collectionRunning): static
    {
        $this->collectionRunning = $collectionRunning;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getExpectedExecutionTime(): ?int
    {
        return $this->expectedExecutionTime;
    }

    public function setExpectedExecutionTime(int $expectedExecutionTime): static
    {
        $this->expectedExecutionTime = $expectedExecutionTime;

        return $this;
    }
}
