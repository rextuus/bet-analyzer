<?php

namespace App\Entity;

use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BettingProviderBackupFileRepository::class)]
class BettingProviderBackupFile
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
    private ?int $containingBets = null;

    #[ORM\Column]
    private ?int $fittedBets = null;

    #[ORM\Column]
    private ?int $alreadyFittedBets = null;

    #[ORM\Column]
    private ?int $nonFittedBets = null;

    #[ORM\Column(type: "string", enumType: BettingProvider::class)]
    private BettingProvider $provider;

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

    public function getContainingBets(): ?int
    {
        return $this->containingBets;
    }

    public function setContainingBets(int $containingBets): static
    {
        $this->containingBets = $containingBets;

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

    public function getAlreadyFittedBets(): ?int
    {
        return $this->alreadyFittedBets;
    }

    public function setAlreadyFittedBets(int $alreadyFittedBets): static
    {
        $this->alreadyFittedBets = $alreadyFittedBets;

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

    public function getProvider(): BettingProvider
    {
        return $this->provider;
    }

    public function setProvider(BettingProvider $provider): BettingProviderBackupFile
    {
        $this->provider = $provider;
        return $this;
    }
}
