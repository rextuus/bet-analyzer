<?php

namespace App\Twig\Components;

use App\Entity\TipicoBet;
use App\Entity\TipicoOverUnderOdd;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationStatisticService;
use App\Twig\Data\UpcomingFixtureOdd;
use DateInterval;
use DateTime;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UpcomingFixture
{
    /**
     * @var TipicoBet[]
     */
    public array $fixtures;

    public ?\App\Entity\Simulator $simulator;

    public BetOn $targetBetOn;
    public BetOn $searchBetOn;
    public ?float $overUnderTarget = null;

    public function getRows(): array
    {
        $mapped = [];
        $current = new DateTime();

        foreach ($this->fixtures as $fixture) {
            $start = (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000);
            $timeDistance = SimulationStatisticService::getTimeDistance($start, $current);

            $expectedEnd = (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000);
            $interval = new DateInterval('PT1H45M');
            $expectedEnd->add($interval);

            $startedClass = 'non-started';
            if ($current > $start) {
                $startedClass = 'started';

                if ($expectedEnd > $current) {
                    $startedClass = 'running';
                }
            }

            $homeGoals = 0;
            $awayGoals = 0;
            $classFinished = 'non-finished';
            $classSimulatorWon = 'non-simulator-won';
            if ($fixture->isFinished()) {
                $homeGoals = $fixture->getEndScoreHome();
                $awayGoals = $fixture->getEndScoreAway();
                $totalGoals = $homeGoals + $awayGoals;
                $result = BetOn::DRAW;
                if ($homeGoals > $awayGoals) {
                    $result = BetOn::HOME;
                }
                if ($awayGoals > $homeGoals) {
                    $result = BetOn::AWAY;
                }

                if ($result === $this->targetBetOn) {
                    $classSimulatorWon = 'simulator-won';
                }

                // over under
                if ($this->targetBetOn === BetOn::OVER && $totalGoals > $this->overUnderTarget) {
                    $classSimulatorWon = 'simulator-won';
                }
                if ($this->targetBetOn === BetOn::UNDER && $totalGoals < $this->overUnderTarget) {
                    $classSimulatorWon = 'simulator-won';
                }

                // both score
                $bothScored = $homeGoals > 0 && $awayGoals > 0;
                if ($this->targetBetOn === BetOn::BOTH_TEAMS_SCORE && $bothScored) {
                    $classSimulatorWon = 'simulator-won';
                }
                if ($this->targetBetOn === BetOn::BOTH_TEAMS_SCORE_NOT && !$bothScored) {
                    $classSimulatorWon = 'simulator-won';
                }

                $classFinished = 'finished';
            }

            $url = '<a href="https://www.google.com/search?q=' . $fixture->getHomeTeamName() . ' - ' . $fixture->getAwayTeamName() . ' ' . $start->format('d.m.y H:i') . ' result" target="_blank">Google</a>';
            $tipicoUrl = '<a href=https://sports.tipico.de/de/heute/default/event/' . $fixture->getTipicoId() . ' result" target="_blank">Tipico</a>';

            $leftOdd = $this->getLeftOddInfo($fixture);
            $middleOdd = $this->getMiddleOddInfo($fixture);
            $rightOdd = $this->getRightOddInfo($fixture);

            $mapped[] = sprintf(
                '<span class="content-container %s %s">
                        <span class="content time">
                            <span class="date">%s</span>
                            <span class="relative">(%s)</span>
                        </span>
                        <span class="content teams">
                            <span class="team">%s</span>
                            <span class="team">%s</span>
                        </span>
                        <span class="content result %s">
                            <span class="result-home">%d</span>
                            <span class="result-home">%d</span>
                        </span>
                        <span class="content odds">
                            <span class="initial-content">
                                <span class="%s">%.2f</span> 
                                <span class="%s">%s</span> 
                                <span class="%s">%.2f</span>
                            </span> 
                            <span class="additional-content">
                            <span class="initial-content">
                               %s
                            </span> 
                            </span>
                            <button class="toggle-btn">All</button>
                        </span> 
                        <span class="content url">
                            <span>%s</span>
                            <span>%s</span>
                        </span>
                        </span>',
                $startedClass,
                $classSimulatorWon,
                $start->format('H:i'),
                $timeDistance,
                $fixture->getHomeTeamName(),
                $fixture->getAwayTeamName(),
                $classFinished,
                $homeGoals,
                $awayGoals,
                $leftOdd->getCssClass(),
                $leftOdd->getOddValue(),
                $middleOdd->getCssClass(),
                $middleOdd->getOddValue(),
                $rightOdd->getCssClass(),
                $rightOdd->getOddValue(),
                $this->getOverUnderOdds($fixture),
                $url,
                $tipicoUrl
            );
        }

        return $mapped;
    }

    public function getOverUnderOdds(TipicoBet $fixture): string
    {
        $odds = $fixture->getTipicoOverUnderOdds()->toArray();
        if (count($odds) !== 6) {
            return '-';
        }

        usort(
            $odds,
            function (TipicoOverUnderOdd $odd1, TipicoOverUnderOdd $odd2) {
                return $odd1->getTargetValue() > $odd2->getTargetValue();
            }
        );

        $target = null;
        if ($this->searchBetOn === BetOn::UNDER || $this->searchBetOn === BetOn::OVER){
            $parameters = json_decode($this->simulator->getStrategy()->getParameters(), true);
            $target = $parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON_TARGET];
        }

        $rows = [];
        foreach ($odds as $index => $odd) {
            $targetClassOver = '';
            $targetClassUnder = '';
            if ((float) $target === $odd->getTargetValue()){
                if($this->searchBetOn === BetOn::OVER){
                    $targetClassOver = 'search-target';
                }
                if($this->searchBetOn === BetOn::UNDER){
                    $targetClassUnder = 'search-target';
                }
            }

            $rows[] = sprintf(
                '
                    <span class="over-under-odds-row-header">%.1f</span> 
                    <span class="over-under-odds-row-value %s">%s</span>     
                    <span class="over-under-odds-row-value %s">%s</span>
                        ',
                $odd->getTargetValue(),
                $targetClassOver,
                $odd->getOverValue(),
                $targetClassUnder,
                $odd->getUnderValue(),
            );
        }

        return sprintf(
            ' 
                    <span class="over-under-odds">
                        Over/Under
                        <span class="over-under-odds-row">
                            %s  
                        </span> 
                         <span class="over-under-odds-row">
                            %s    
                        </span> 
                        <span class="over-under-odds-row">
                            %s    
                        </span> 
                        <span class="over-under-odds-row">
                            %s  
                        </span> 
                         <span class="over-under-odds-row">
                            %s    
                        </span> 
                        <span class="over-under-odds-row">
                            %s    
                        </span> 
                    </span>
                    ',
            $rows[0],
            $rows[1],
            $rows[2],
            $rows[3],
            $rows[4],
            $rows[5],
        );
    }

    private function getLeftOddInfo(TipicoBet $fixture): UpcomingFixtureOdd
    {
        $oddInfo = new UpcomingFixtureOdd();
        match ($this->targetBetOn) {
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY => $value = $fixture->getOddHome(),
            BetOn::OVER, BetOn::UNDER => $value = $this->getOverOdd($fixture),
            BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT => $value = $fixture->getTipicoBothTeamsScoreBet()->getConditionTrueValue(),
            default => $value = -100,
        };
        $oddInfo->setOddValue($value);

        match ($this->targetBetOn) {
            BetOn::HOME, BetOn::OVER, BetOn::BOTH_TEAMS_SCORE => $cssClass = 'target',
            BetOn::DRAW, BetOn::AWAY, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = 'non-target',
            default => $cssClass = 'target-notdefined',
        };
        $oddInfo->setCssClass($cssClass);

        return $oddInfo;
    }

    private function getRightOddInfo(TipicoBet $fixture): UpcomingFixtureOdd
    {
        $oddInfo = new UpcomingFixtureOdd();
        match ($this->targetBetOn) {
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY => $value = $fixture->getOddAway(),
            BetOn::OVER, BetOn::UNDER => $value = $this->getUnderOdd($fixture),
            BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT => $value = $fixture->getTipicoBothTeamsScoreBet()->getConditionFalseValue(),
        };
        $oddInfo->setOddValue($value);

        match ($this->targetBetOn) {
            BetOn::AWAY, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = 'target',
            BetOn::HOME, BetOn::DRAW, BetOn::OVER, BetOn::BOTH_TEAMS_SCORE => $cssClass = 'non-target',
        };
        $oddInfo->setCssClass($cssClass);

        return $oddInfo;
    }

    private function getMiddleOddInfo(TipicoBet $fixture): UpcomingFixtureOdd
    {
        $oddInfo = new UpcomingFixtureOdd();
        match ($this->targetBetOn) {
            BetOn::DRAW, BetOn::HOME, BetOn::AWAY, => $value = $fixture->getOddDraw(),
            BetOn::OVER, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT => $value = 0.0,
        };
        $oddInfo->setOddValue($value);

        match ($this->targetBetOn) {
            BetOn::DRAW, => $cssClass = 'target',
            BetOn::HOME, BetOn::AWAY, BetOn::OVER, BetOn::BOTH_TEAMS_SCORE, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = 'non-target',
        };
        $oddInfo->setCssClass($cssClass);

        return $oddInfo;
    }

    private function getOverOdd(TipicoBet $fixture): float
    {
        foreach ($fixture->getTipicoOverUnderOdds() as $overUnderOdd) {
            if ($overUnderOdd->getTargetValue() === $this->overUnderTarget) {
                return $overUnderOdd->getOverValue();
            }
        }
        return 0.0;
    }

    private function getUnderOdd(TipicoBet $fixture): float
    {
        foreach ($fixture->getTipicoOverUnderOdds() as $overUnderOdd) {
            if ($overUnderOdd->getTargetValue() === $this->overUnderTarget) {
                return $overUnderOdd->getUnderValue();
            }
        }
        return 0.0;
    }
}
