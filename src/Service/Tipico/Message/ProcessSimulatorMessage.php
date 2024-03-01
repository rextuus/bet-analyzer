<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class ProcessSimulatorMessage
{
    private int $simulatorId;

    public function __construct(int $simulatorId)
    {
        $this->simulatorId = $simulatorId;
    }

    public function getSimulatorId(): int
    {
        return $this->simulatorId;
    }

    public function setSimulatorId(int $simulatorId): ProcessSimulatorMessage
    {
        $this->simulatorId = $simulatorId;
        return $this;
    }
}
