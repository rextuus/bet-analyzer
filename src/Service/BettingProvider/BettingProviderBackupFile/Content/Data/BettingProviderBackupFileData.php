<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile\Content\Data;

use App\Entity\BettingProvider\BettingProviderBackupFile;
use App\Service\BettingProvider\BettingProvider;
use DateTimeInterface;


class BettingProviderBackupFileData
{
    private DateTimeInterface $created;
    private string $filePath;
    private bool $isConsumed;
    private int $containingBets;
    private int $fittedBets;
    private int $alreadyFittedBets;
    private int $nonFittedBets;
    private BettingProvider $provider;

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): BettingProviderBackupFileData
    {
        $this->created = $created;
        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): BettingProviderBackupFileData
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function isConsumed(): bool
    {
        return $this->isConsumed;
    }

    public function setIsConsumed(bool $isConsumed): BettingProviderBackupFileData
    {
        $this->isConsumed = $isConsumed;
        return $this;
    }

    public function getContainingBets(): int
    {
        return $this->containingBets;
    }

    public function setContainingBets(int $containingBets): BettingProviderBackupFileData
    {
        $this->containingBets = $containingBets;
        return $this;
    }

    public function getFittedBets(): int
    {
        return $this->fittedBets;
    }

    public function setFittedBets(int $fittedBets): BettingProviderBackupFileData
    {
        $this->fittedBets = $fittedBets;
        return $this;
    }

    public function getAlreadyFittedBets(): int
    {
        return $this->alreadyFittedBets;
    }

    public function setAlreadyFittedBets(int $alreadyFittedBets): BettingProviderBackupFileData
    {
        $this->alreadyFittedBets = $alreadyFittedBets;
        return $this;
    }

    public function getNonFittedBets(): int
    {
        return $this->nonFittedBets;
    }

    public function setNonFittedBets(int $nonFittedBets): BettingProviderBackupFileData
    {
        $this->nonFittedBets = $nonFittedBets;
        return $this;
    }

    public function getProvider(): BettingProvider
    {
        return $this->provider;
    }

    public function setProvider(BettingProvider $provider): BettingProviderBackupFileData
    {
        $this->provider = $provider;
        return $this;
    }

    public function initFromEntity(BettingProviderBackupFile $backup): BettingProviderBackupFileData
    {
        $this->created = $backup->getCreated();
        $this->filePath = $backup->getFilePath();
        $this->containingBets = $backup->getContainingBets();
        $this->alreadyFittedBets = $backup->getAlreadyFittedBets();
        $this->nonFittedBets = $backup->getNonFittedBets();
        $this->fittedBets = $backup->getFittedBets();
        $this->isConsumed = $backup->isIsConsumed();
        $this->provider = $backup->getProvider();

        return $this;
    }
}
