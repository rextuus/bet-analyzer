<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement\Data;

use PHPUnit\Util\Exception;


class LastWeekStatisticData
{
    /**
     * @var float[]
     */
    private array $changeVolume = [];

    /**
     * @var int[]
     */
    private array $madeBets = [];

    /**
     * @var string[]
     */
    private array $weekDays = [];

    /**
     * @var string[]
     */
    private array $formattedDates = [];

    /**
     * @var int[]
     */
    private array $ranks = [];

    public function getChangeVolume(): array
    {
        return $this->changeVolume;
    }

    public function setChangeVolume(array $changeVolume): LastWeekStatisticData
    {
        $this->changeVolume = $changeVolume;
        return $this;
    }

    public function getMadeBets(): array
    {
        return $this->madeBets;
    }

    public function setMadeBets(array $madeBets): LastWeekStatisticData
    {
        $this->madeBets = $madeBets;
        return $this;
    }

    public function getWeekDays(): array
    {
        return $this->weekDays;
    }

    public function setWeekDays(array $weekDays): LastWeekStatisticData
    {
        $this->weekDays = $weekDays;
        return $this;
    }

    public function getFormattedDates(): array
    {
        return $this->formattedDates;
    }

    public function setFormattedDates(array $formattedDates): LastWeekStatisticData
    {
        $this->formattedDates = $formattedDates;
        return $this;
    }

    public function getRanks(): array
    {
        return $this->ranks;
    }

    public function setRanks(array $ranks): LastWeekStatisticData
    {
        $this->ranks = $ranks;
        return $this;
    }

    public function calculateRanks(): LastWeekStatisticData
    {
        $changes = $this->getChangeVolume();
        asort($changes);

        $ranks = [];
        $rank = 0;
        foreach ($changes as $key => $value) {
            $ranks[$key] = $rank;
            $rank++;
        }
        $this->setRanks($ranks);

        return $this;
    }

    public function addValueByArrayKey(string $key, int|float|string|null $value): LastWeekStatisticData
    {
        match ($key) {
            'changeVolume' => $this->changeVolume[] = $value ?: 0.0,
            'madeBets' => $this->madeBets[] = $value?: 0,
            'weekday' => $this->weekDays[] = $value,
            'formatted' => $this->formattedDates[] = $value,
            'default' => throw new Exception('LastWeekStatistic cant handle key: ' . $key),
        };

        return $this;
    }
}
