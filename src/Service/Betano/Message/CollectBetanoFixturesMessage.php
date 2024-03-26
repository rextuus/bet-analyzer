<?php
declare(strict_types=1);

namespace App\Service\Betano\Message;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class CollectBetanoFixturesMessage
{
    private int $lastExecutionTime;

    public function __construct()
    {

    }

    public function getLastExecutionTime(): int
    {
        return $this->lastExecutionTime;
    }

    public function setLastExecutionTime(int $lastExecutionTime): CollectBetanoFixturesMessage
    {
        $this->lastExecutionTime = $lastExecutionTime;
        return $this;
    }
}
