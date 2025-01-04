<?php

namespace App\Service\Evaluation\Message;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
final class TriggerBetRowsForSeasonMessage
{
    private int $seasonApiId;

    private bool $useEnd;

    public function __construct(int $seasonApiId, bool $useEnd)
    {
        $this->seasonApiId = $seasonApiId;
        $this->useEnd = $useEnd;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): TriggerBetRowsForSeasonMessage
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function isUseEnd(): bool
    {
        return $this->useEnd;
    }

    public function setUseEnd(bool $useEnd): TriggerBetRowsForSeasonMessage
    {
        $this->useEnd = $useEnd;
        return $this;
    }
}
