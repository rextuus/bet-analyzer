<?php

namespace App\Entity;

use App\Service\Betano\Content\BetanoBackup\BetanoBackupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetanoBackupRepository::class)]
class BetanoBackup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\Column]
    private ?bool $isConsumed = null;

    #[ORM\Column]
    private ?int $containedBets = null;

    #[ORM\Column]
    private ?int $fittedBets = null;

    #[ORM\Column]
    private ?int $alreadyStoredBets = null;

    #[ORM\Column]
    private ?int $nonFittedBets = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function isIsConsumed(): ?bool
    {
        return $this->isConsumed;
    }

    public function setIsConsumed(bool $isConsumed): static
    {
        $this->isConsumed = $isConsumed;

        return $this;
    }

    public function getContainedBets(): ?int
    {
        return $this->containedBets;
    }

    public function setContainedBets(int $containedBets): static
    {
        $this->containedBets = $containedBets;

        return $this;
    }

    public function getFittedBets(): ?int
    {
        return $this->fittedBets;
    }

    public function setFittedBets(int $fittedBets): static
    {
        $this->fittedBets = $fittedBets;

        return $this;
    }

    public function getAlreadyStoredBets(): ?int
    {
        return $this->alreadyStoredBets;
    }

    public function setAlreadyStoredBets(int $alreadyStoredBets): static
    {
        $this->alreadyStoredBets = $alreadyStoredBets;

        return $this;
    }

    public function getNonFittedBets(): ?int
    {
        return $this->nonFittedBets;
    }

    public function setNonFittedBets(int $nonFittedBets): static
    {
        $this->nonFittedBets = $nonFittedBets;

        return $this;
    }
}
