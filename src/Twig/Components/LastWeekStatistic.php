<?php

namespace App\Twig\Components;

use App\Service\Tipico\Content\Placement\Data\LastWeekStatisticData;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class LastWeekStatistic
{
    private const RANKS =
        [
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
        ];

    public LastWeekStatisticData $statisticData;

    public function getTotalCount(): int
    {
        return count($this->statisticData->getChangeVolume()) - 1;
    }

    public function getTotalChange(): float
    {
        return array_sum($this->statisticData->getChangeVolume());
    }

    public function getRow(int $rowNr): string
    {
        $changes = $this->statisticData->getChangeVolume();

        $change = $changes[$rowNr];
        $madeBets = $this->statisticData->getMadeBets()[$rowNr];
        $weekDay = $this->statisticData->getWeekDays()[$rowNr];
        $formatted = $this->statisticData->getFormattedDates()[$rowNr];
        $rank = self::RANKS[$this->statisticData->getRanks()[$rowNr]];

        $changeClass = 'change-negative';

        if ($change > 0.0) {
            $changeClass = 'change-positive';
        }

        if ($change === 0.0) {
            $changeClass = 'change-none';
            $rank = 'zero';
        }
        if ($rowNr === $this->getTotalCount()){
            $weekDay = 'Today';
        }

        return sprintf(
            '<span class="week-statistic-entry">
                        <span class="week-statistic-entry-col %s %s">
                            <span class="weekday">%s</span>
                            <span class="bets">%d bets</span>
                            <span class="change">%.2f €</span>
                        </span>
                    </span>',
            $changeClass,
            $rank,
            $weekDay,
            $madeBets,
            $change,
        );
    }
}
