<?php
declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile\Message;

use App\Service\BettingProvider\BettingProvider;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class StoreBetsForProviderMessage
{
    private int $backupId;
    private BettingProvider $bettingProvider;

    public function __construct(int $backupFilePath, BettingProvider $bettingProvider)
    {
        $this->backupId = $backupFilePath;
        $this->bettingProvider = $bettingProvider;
    }

    public function getBackupId(): int
    {
        return $this->backupId;
    }

    public function setBackupId(int $backupId): StoreBetsForProviderMessage
    {
        $this->backupId = $backupId;
        return $this;
    }

    public function getBettingProvider(): BettingProvider
    {
        return $this->bettingProvider;
    }

    public function setBettingProvider(BettingProvider $bettingProvider): StoreBetsForProviderMessage
    {
        $this->bettingProvider = $bettingProvider;
        return $this;
    }
}
