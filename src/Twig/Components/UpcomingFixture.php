<?php

namespace App\Twig\Components;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Twig\Components\Helper\FixruteVisualizationTrait;
use DateTime;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UpcomingFixture
{
    use FixruteVisualizationTrait;

    /**
     * @var TipicoBet[]
     */
    public array $fixtures;

    public ?\App\Entity\BettingProvider\Simulator $simulator;

    public BetOn $targetBetOn;
    public BetOn $searchBetOn;
    public bool $isActiveOnWeekDay = true;
    public ?float $overUnderTarget = 0.0;

    public function getRows(): array
    {
        $mapped = [];

        foreach ($this->fixtures as $fixture) {
            $start = (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000);

            $startedClass = $this->getStartedCssClass($fixture);

            $matchInfo = $this->getMatchInfo(
                $fixture,
                $this->isActiveOnWeekDay,
                $this->targetBetOn,
                $this->overUnderTarget ?: 0.0
            );

            $cssClasses = $this->calculateOddMatrix($this->simulator);
            $mapped[] = $this->calculateHtml(
                $matchInfo,
                $startedClass,
                $start,
                $this->getTimeDistance($fixture),
                $fixture,
                $cssClasses,
            );
        }

        return $mapped;
    }
}
