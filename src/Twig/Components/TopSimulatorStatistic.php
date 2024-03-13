<?php

namespace App\Twig\Components;

use App\Service\Tipico\Content\Placement\Data\TopSimulatorStatisticData;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TopSimulatorStatistic
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
            'nine',
            'ten',
        ];

    public TopSimulatorStatisticData $statisticData;
    public bool $isCurrentVariant = false;

    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function getTotalCount(): int
    {
        return count($this->statisticData->getChangeVolumes()) - 1;
    }

    public function getRange(): string
    {
        if ($this->isCurrentVariant){
            return 'Today';
        }

        return sprintf(
            '%s - %s',
            $this->statisticData->getFrom()->format('d.m.y'),
            $this->statisticData->getUntil()->format('d.m.y'),
        );
    }

    public function getRow(int $rowNr): string
    {
        $change = $this->statisticData->getChangeVolumes()[$rowNr];
        $madeBets = $this->statisticData->getMadeBets()[$rowNr];
        $ident = $this->statisticData->getSimulatorIdents()[$rowNr];
        $id = $this->statisticData->getSimulatorIds()[$rowNr];
        $rank = self::RANKS[$this->statisticData->getRanks()[$rowNr]];

        if ($change === 0.0) {
            $rank = 'zero';
        }

        return sprintf(
            '<span class="top-simulator-statistic-entry">
                        <span class="week-statistic-entry-col %s">
                            <span class="name"><a href="%s">%s</a></span>
                            <span class="bets">%d bets</span>
                            <span class="change">%.2f €</span>
                        </span>
                    </span>',
            $rank,
            $this->router->generate('app_tipico_simulation_detail', ['simulator' => $id]),
            $ident,
            $madeBets,
            $change,
        );
    }
}
