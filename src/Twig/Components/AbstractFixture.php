<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\BettingProvider\TipicoBet;
use App\Entity\BettingProvider\TipicoOverUnderOdd;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OddVariant;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class AbstractFixture
{
    protected function getThreeWayOdds(TipicoBet $fixture, array $cssClasses): string
    {
        $classHome = $cssClasses[OddVariant::CLASSIC_3_WAY->name][0];
        $classDraw = $cssClasses[OddVariant::CLASSIC_3_WAY->name][1];
        $classAway = $cssClasses[OddVariant::CLASSIC_3_WAY->name][2];

        $bwinHome = 0.0;
        $bwinDraw = 0.0;
        $bwinAway = 0.0;
        if ($fixture->getBwinBet()) {
            $bwinHome = $fixture->getBwinBet()->getOddHome();
            $bwinDraw = $fixture->getBwinBet()->getOddDraw();
            $bwinAway = $fixture->getBwinBet()->getOddAway();
        }

        $betanoHome = 0.0;
        $betanoDraw = 0.0;
        $betanoAway = 0.0;
        if ($fixture->getCorrespondedBetanoBet()) {
            $betanoHome = $fixture->getCorrespondedBetanoBet()->getOddHome();
            $betanoDraw = $fixture->getCorrespondedBetanoBet()->getOddDraw();
            $betanoAway = $fixture->getCorrespondedBetanoBet()->getOddAway();
        }

        return sprintf(
            ' 
                     <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    <span class="three-way-odds-row">
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                        <span class="three-way-odds-row-value %s">%.2f</span>
                    </span> 
                    ',
            $classHome,
            $fixture->getOddHome(),
            $classHome,
            $betanoHome,
            $classDraw,
            $bwinHome,
            $classDraw,
            $fixture->getOddDraw(),
            $classDraw,
            $betanoDraw,
            $classDraw,
            $bwinDraw,
            $classAway,
            $fixture->getOddAway(),
            $classAway,
            $betanoAway,
            $classAway,
            $bwinAway,
        );
    }

    protected function getOverUnderOdds(TipicoBet $fixture, array $cssClasses): string
    {
        $odds = $fixture->getTipicoOverUnderOdds()->toArray();
        if (count($odds) !== 6) {
            return '-';
        }

        usort(
            $odds,
            function (TipicoOverUnderOdd $odd1, TipicoOverUnderOdd $odd2) {
                if ($odd1->getTargetValue() > $odd2->getTargetValue()) {
                    return 1;
                }
                if ($odd1->getTargetValue() < $odd2->getTargetValue()) {
                    return -1;
                }
                return 0;
            }
        );

        $rows = [];
        $counter = 0;
        foreach ($odds as $index => $odd) {
            $rows[] = sprintf(
                '
                    <span class="over-under-odds-row-header">%.1f:</span> 
                    <span class="over-under-odds-row-value %s">%.2f</span>     
                    <span class="over-under-odds-row-value %s">%.2f</span>
                        ',
                $odd->getTargetValue(),
                $cssClasses[OddVariant::OVER_UNDER->name][$counter],
                $odd->getOverValue(),
                $cssClasses[OddVariant::OVER_UNDER->name][$counter + 1],
                $odd->getUnderValue(),
            );
            $counter = $counter + 2;
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

    protected function calculateOddMatrix(
        \App\Entity\BettingProvider\Simulator $simulator,
        bool $search = true,
        bool $target = true
    ): array {
        $strategy = $simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);

        $searchBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
        $targetBeton = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);

        $overUnderSearchTarget = 0.0;
        if ($searchBetOn === BetOn::OVER || $searchBetOn === BetOn::UNDER) {
            $overUnderSearchTarget = (float)$parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON_TARGET];
        }

        $overUnderTargetTarget = 0.0;
        if ($targetBeton === BetOn::OVER || $targetBeton === BetOn::UNDER) {
            $overUnderTargetTarget = (float)$parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE];
        }

        $classes = [];
        $oddVariants = OddVariant::cases();
        foreach ($oddVariants as $oddVariant) {
            $searchPrefix = 'search';
            if (!$search) {
                $searchPrefix = '';
            }

            match ($oddVariant) {
                OddVariant::CLASSIC_3_WAY => $searchVariant = $this->getThreeWayOddsCssClasses(
                    $searchBetOn,
                    $searchPrefix
                ),
                OddVariant::BOTH_TEAMS_SCORE => $searchVariant = $this->getBothTeamsScoreCssClasses(
                    $searchBetOn,
                    $searchPrefix
                ),
                OddVariant::HEAD_TO_HEAD => $searchVariant = $this->getHeadToHeadCssClasses(
                    $searchBetOn,
                    $searchPrefix
                ),
                OddVariant::OVER_UNDER => $searchVariant = $this->getOverUnderCssClasses(
                    $searchBetOn,
                    $overUnderSearchTarget,
                    $searchPrefix
                ),
            };

            $targetPrefix = 'target';
            if (!$target) {
                $targetPrefix = '';
            }
            match ($oddVariant) {
                OddVariant::CLASSIC_3_WAY => $targetVariant = $this->getThreeWayOddsCssClasses(
                    $targetBeton,
                    $targetPrefix
                ),
                OddVariant::BOTH_TEAMS_SCORE => $targetVariant = $this->getBothTeamsScoreCssClasses(
                    $targetBeton,
                    $targetPrefix
                ),
                OddVariant::HEAD_TO_HEAD => $targetVariant = $this->getHeadToHeadCssClasses(
                    $targetBeton,
                    $targetPrefix
                ),
                OddVariant::OVER_UNDER => $targetVariant = $this->getOverUnderCssClasses(
                    $targetBeton,
                    $overUnderTargetTarget,
                    $targetPrefix
                ),
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

    private function getThreeWayOddsCssClasses(BetOn $betOn, string $prefix): array
    {
        $target = 'is-' . $prefix;
        $nonTarget = 'is-not-' . $prefix;
        match ($betOn) {
            BetOn::HOME => $text = [$target, 'non-search-target-odd', $nonTarget],
            BetOn::DRAW => $text = [$nonTarget, $target, $nonTarget],
            BetOn::AWAY => $text = [$nonTarget, $nonTarget, $target],
            BetOn::H2H_HOME, BetOn::BOTH_TEAMS_SCORE, BetOn::H2H_AWAY, BetOn::BOTH_TEAMS_SCORE_NOT, BetOn::OVER, BetOn::UNDER => $text = [
                $nonTarget,
                $nonTarget,
                $nonTarget
            ],
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
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY, BetOn::OVER, BetOn::UNDER, BetOn::BOTH_TEAMS_SCORE, BetOn::BOTH_TEAMS_SCORE_NOT => $cssClass = [
                $nonTarget,
                $nonTarget
            ],
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
            BetOn::HOME, BetOn::DRAW, BetOn::AWAY, BetOn::OVER, BetOn::UNDER, BetOn::H2H_HOME, BetOn::H2H_AWAY => $cssClass = [
                $nonTarget,
                $nonTarget
            ],
        };

        return $cssClass;
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
}
