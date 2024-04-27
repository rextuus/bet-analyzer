<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use DateTimeInterface;


class CombinationMadeBetEvent
{
    private string $shortName;
    private string $fullName;
    private DateTimeInterface $eventDate;
    private int $occurrence;
    private float $outcome;
    private float $odd;

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): CombinationMadeBetEvent
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): CombinationMadeBetEvent
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getEventDate(): DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(DateTimeInterface $eventDate): CombinationMadeBetEvent
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    public function getOccurrence(): int
    {
        return $this->occurrence;
    }

    public function setOccurrence(int $occurrence): CombinationMadeBetEvent
    {
        $this->occurrence = $occurrence;
        return $this;
    }

    public function getOutcome(): float
    {
        return $this->outcome;
    }

    public function setOutcome(float $outcome): CombinationMadeBetEvent
    {
        $this->outcome = $outcome;
        return $this;
    }

    public function getOdd(): float
    {
        return $this->odd;
    }

    public function setOdd(float $odd): CombinationMadeBetEvent
    {
        $this->odd = $odd;
        return $this;
    }
}
