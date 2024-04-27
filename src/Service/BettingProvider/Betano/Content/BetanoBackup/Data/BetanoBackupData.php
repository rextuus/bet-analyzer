<?php
declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Content\BetanoBackup\Data;

use App\Entity\BetanoBackup;
use DateTimeInterface;


class BetanoBackupData
{
    private DateTimeInterface $created;

    private string $filePath;

    private bool $isConsumed;

    private int $containedBets;

    private int $fittedBets;

    private int $alreadyStoredBets;

    private int $nonFittedBets;

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): BetanoBackupData
    {
        $this->created = $created;
        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): BetanoBackupData
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function isConsumed(): bool
    {
        return $this->isConsumed;
    }

    public function setIsConsumed(bool $isConsumed): BetanoBackupData
    {
        $this->isConsumed = $isConsumed;
        return $this;
    }

    public function getContainedBets(): int
    {
        return $this->containedBets;
    }

    public function setContainedBets(int $containedBets): BetanoBackupData
    {
        $this->containedBets = $containedBets;
        return $this;
    }

    public function getFittedBets(): int
    {
        return $this->fittedBets;
    }

    public function setFittedBets(int $fittedBets): BetanoBackupData
    {
        $this->fittedBets = $fittedBets;
        return $this;
    }

    public function getAlreadyStoredBets(): int
    {
        return $this->alreadyStoredBets;
    }

    public function setAlreadyStoredBets(int $alreadyStoredBets): BetanoBackupData
    {
        $this->alreadyStoredBets = $alreadyStoredBets;
        return $this;
    }

    public function getNonFittedBets(): int
    {
        return $this->nonFittedBets;
    }

    public function setNonFittedBets(int $nonFittedBets): BetanoBackupData
    {
        $this->nonFittedBets = $nonFittedBets;
        return $this;
    }

    public function initFromEntity(BetanoBackup $backup): BetanoBackupData
    {
        $this->created = $backup->getCreated();
        $this->filePath = $backup->getFilePath();
        $this->containedBets = $backup->getContainedBets();
        $this->alreadyStoredBets = $backup->getAlreadyStoredBets();
        $this->nonFittedBets = $backup->getNonFittedBets();
        $this->fittedBets = $backup->getFittedBets();
        $this->isConsumed = $backup->isIsConsumed();

        return $this;
    }
}
