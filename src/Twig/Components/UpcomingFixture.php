<?php

namespace App\Twig\Components;

use App\Entity\TipicoBet;
use App\Entity\TipicoOverUnderOdd;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OddVariant;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\BothTeamsScoreStrategy;
use App\Service\Tipico\SimulationProcessors\HeadToHeadStrategy;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimpleStrategy;
use App\Service\Tipico\SimulationProcessors\SimulationProcessorInterface;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
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

    public function __construct(private SimulationStrategyProcessorProvider $provider)
    {
    }

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
            $cssClasses = $this->calculateOddMatrix();
//            dd($cssClasses);
            $mapped[] = sprintf(
                '<span class="content-container %s %s">
                            <span class="upper-col">
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

                                <span class="three-way-odds">
                                %s
                                </span>
                                <span class="content url">
                                    <span>%s</span>
                                    <span>%s</span>
                                </span>
                            </span>
                            <span class="under-col">
                                <span>%s</span>
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
                $this->getThreeWayOdds($fixture, $cssClasses),
                $url,
                $tipicoUrl,
                $this->getOverUnderOdds($fixture, $cssClasses),
                $this->getBothTeamsScoreOdds($fixture, $cssClasses),
                $this->getHeadToHeadOdds($fixture, $cssClasses),

            );
        }

        return $mapped;
    }

    public function getThreeWayOdds(TipicoBet $fixture, array $cssClasses): string
    {
        return sprintf(
            ' 
                     <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    ',
            $cssClasses[OddVariant::CLASSIC_3_WAY->name][0],
            $fixture->getOddHome(),
            $cssClasses[OddVariant::CLASSIC_3_WAY->name][1],
            $fixture->getOddDraw(),
            $cssClasses[OddVariant::CLASSIC_3_WAY->name][2],
            $fixture->getOddAway(),
        );
    }

    public function getHeadToHeadOdds(TipicoBet $fixture, array $cssClasses): string
    {
        $odd = $fixture->getTipicoHeadToHeadScore();
        if ($odd === null) {
            return '-';
        }

        return sprintf(
            ' 
                    H2H
                    <span class="head-to-head-odds">
                             <span class="head-to-head-odds-row">
                                <span class="head-to-head-odds-row-value %s">%.2f</span>
                            </span> 
                            <span class="head-to-head-odds-row">
                                <span class="head-to-head-odds-row-value %s">%.2f</span>
                            </span> 
                    </span>
                    ',
            $cssClasses[OddVariant::HEAD_TO_HEAD->name][0],
            $odd->getHomeTeamValue(),
            $cssClasses[OddVariant::HEAD_TO_HEAD->name][1],
            $odd->getAwayTeamValue(),
        );
    }

    public function getBothTeamsScoreOdds(TipicoBet $fixture, array $cssClasses): string
    {
        $odd = $fixture->getTipicoBothTeamsScoreBet();
        if ($odd === null) {
            return '-';
        }

        return sprintf(
            ' 
                    Both Score
                    <span class="both-teams-score-odds">
                             <span class="both-teams-score-odds-row">
                                <span class="both-teams-score-odds-row-value %s">%.2f</span>
                            </span> 
                            <span class="both-teams-score-odds-row">
                                <span class="both-teams-score-odds-row-value %s">%.2f</span>
                            </span> 
                    </span>
                    ',
            $cssClasses[OddVariant::BOTH_TEAMS_SCORE->name][0],
            $odd->getConditionTrueValue(),
            $cssClasses[OddVariant::BOTH_TEAMS_SCORE->name][1],
            $odd->getConditionFalseValue(),
        );
    }

    public function getOverUnderOdds(TipicoBet $fixture, array $cssClasses): string
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

        $rows = [];
        foreach ($odds as $index => $odd) {
            $rows[] = sprintf(
                '
                    <span class="over-under-odds-row-header">%.1f:</span> 
                    <span class="over-under-odds-row-value %s">%.2f</span>     
                    <span class="over-under-odds-row-value %s">%.2f</span>
                        ',
                $odd->getTargetValue(),
                $cssClasses[OddVariant::OVER_UNDER->name][$index],
                $odd->getOverValue(),
                $cssClasses[OddVariant::OVER_UNDER->name][$index+1],
                $odd->getUnderValue(),
            );
        }

        return sprintf(
            ' 
                    Over/Under
                    <span class="over-under-odds">
                        <span class="half-1">
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
                        <span class="half-2">
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

    private function calculateOddMatrix(): array
    {
        $strategy = $this->simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);
        $overUnderSearchTarget = (float)$parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON_TARGET];

        $searchBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
        $targetBeton = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);

        $overUnderTargetTarget = 0.0;
        if ($targetBeton === BetOn::OVER || $targetBeton === BetOn::UNDER) {
            $overUnderTargetTarget = (float)$parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE];
        }

        $classes = [];
        $oddVariants = OddVariant::cases();
        foreach ($oddVariants as $oddVariant) {
            match ($oddVariant) {
                OddVariant::CLASSIC_3_WAY => $searchVariant = $this->getThreeWayOddsCssClasses($searchBetOn, 'search'),
                OddVariant::BOTH_TEAMS_SCORE => $searchVariant = $this->getBothTeamsScoreCssClasses($searchBetOn, 'search'),
                OddVariant::HEAD_TO_HEAD => $searchVariant = $this->getHeadToHeadCssClasses($searchBetOn, 'search'),
                OddVariant::OVER_UNDER => $searchVariant = $this->getOverUnderCssClasses($searchBetOn, $overUnderSearchTarget, 'search'),
            };

            match ($oddVariant) {
                OddVariant::CLASSIC_3_WAY => $targetVariant = $this->getThreeWayOddsCssClasses($targetBeton, 'target'),
                OddVariant::BOTH_TEAMS_SCORE => $targetVariant = $this->getBothTeamsScoreCssClasses($targetBeton, 'target'),
                OddVariant::HEAD_TO_HEAD => $targetVariant = $this->getHeadToHeadCssClasses($targetBeton, 'target'),
                OddVariant::OVER_UNDER => $targetVariant = $this->getOverUnderCssClasses($targetBeton, $overUnderTargetTarget, 'target'),
            };

            $result = [];
            foreach ($searchVariant as $searchCssClassIndex => $searchCssClass) {
                if (is_array($searchCssClass)) {
                    foreach ($searchCssClass as $targetCssClassIndex => $targetCssClass) {
                        $result[] = $targetCssClass . ' ' . $targetVariant[$searchCssClassIndex][$targetCssClassIndex];

                    }
                } else {
                    $result[] = $searchCssClass . ' ' . $targetVariant[$searchCssClassIndex];
                }
            }

            $classes[$oddVariant->name] = $result;
        }

        return $classes;
    }

    private function getOverUnderCssClasses(BetOn $betOn, float $overUnderTarget, string $prefix): array
    {
        $target = 'is-' . $prefix;
        $nonTarget = 'is-not-' . $prefix;

        $variants = [0.5, 1.5, 2.5, 3.5, 4.5, 5.5];
        $matrix = [];
        foreach ($variants as $key => $variant) {
            $matrix[] = [$nonTarget, $nonTarget];
            if ($variant === $overUnderTarget) {
                if ($betOn === BetOn::OVER) {
                    $matrix[$key] = [$target, $nonTarget];
                }
                if ($betOn === BetOn::UNDER) {
                    $matrix[$key] = [$nonTarget, $target];
                }
            }
        }

        return $matrix;
    }

    private function getThreeWayOddsCssClasses(BetOn $betOn, string $prefix): array
    {
        $target = 'is-' . $prefix;
        $nonTarget = 'is-not-' . $prefix;
        match ($betOn) {
            BetOn::HOME => $text = [$target, 'non-search-target-odd', $nonTarget],
            BetOn::DRAW => $text = [$nonTarget, $target, $nonTarget],
            BetOn::AWAY => $text = [$nonTarget, $nonTarget, $target],
            BetOn::H2H_HOME, BetOn::BOTH_TEAMS_SCORE, BetOn::H2H_AWAY, BetOn::BOTH_TEAMS_SCORE_NOT, BetOn::OVER, BetOn::UNDER => $text = [$nonTarget, $nonTarget, $nonTarget],
        };

        return $text;
    }

    private function getHeadToHeadCssClasses(BetOn $betOn, string $prefix): array
    {
        $target = 'is-' . $prefix;
        $nonTarget = 'is-not-' . $prefix;
        match ($betOn) {
            BetOn::H2H_HOME => $cssClass = [$target, $nonTarget],
            BetOn::H2H_AWAY => $cssClass = [$nonTarget, $target],
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY, BetOn::OVER, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = [$nonTarget, $nonTarget],
        };

        return $cssClass;
    }

    private function getBothTeamsScoreCssClasses(BetOn $betOn, string $prefix): array
    {
        $target = 'is-' . $prefix;
        $nonTarget = 'is-not-' . $prefix;
        match ($betOn) {
            BetOn::BOTH_TEAMS_SCORE => $cssClass = [$target, $nonTarget],
            BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = [$nonTarget, $target],
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY, BetOn::OVER, BetOn::UNDER, BetOn::H2H_HOME, BetOn::H2H_AWAY => $cssClass = [$nonTarget, $nonTarget],
        };

        return $cssClass;
    }
}
