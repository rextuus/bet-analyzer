<?php

namespace App\Service\Tipico\Message;

final class CreateOrUpdateDetailStatisticMessage
{
    public function __construct(private int $simulatorId)
    {
    }

    public function getSimulatorId(): int
    {
        return $this->simulatorId;
    }

    public function setSimulatorId(int $simulatorId): CreateOrUpdateDetailStatisticMessage
    {
        $this->simulatorId = $simulatorId;
        return $this;
    }
}