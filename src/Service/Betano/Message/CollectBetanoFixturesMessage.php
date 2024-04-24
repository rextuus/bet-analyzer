<?php
declare(strict_types=1);

namespace App\Service\Betano\Message;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class CollectBetanoFixturesMessage
{
    private int $backupId;

    public function __construct(int $backupFilePath)
    {
        $this->backupId = $backupFilePath;
    }

    public function getBackupId(): int
    {
        return $this->backupId;
    }

    public function setBackupId(int $backupId): CollectBetanoFixturesMessage
    {
        $this->backupId = $backupId;
        return $this;
    }
}
