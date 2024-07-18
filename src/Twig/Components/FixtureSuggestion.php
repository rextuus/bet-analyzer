<?php

namespace App\Twig\Components;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationStatisticService;
use App\Service\Tipico\Suggestion\BetPlacementSuggestion;
use DateInterval;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class FixtureSuggestion extends AbstractFixture
{
    public BetPlacementSuggestion $betPlacementSuggestion;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }


    public function simulatorDetail(\App\Entity\BettingProvider\Simulator $simulator): string
    {
        $total = Weekday::getSimulatorWeekDayTotalValue($simulator, $this->betPlacementSuggestion->getWeekday());
        $average = Weekday::getSimulatorWeekDayAverageValue($simulator, $this->betPlacementSuggestion->getWeekday());

        return sprintf(
            '<a href="%s">%s</a>: <span class="badge">%.2f</span> <span class="badge">%s€ (%s€/bet)</span>',
            $this->urlGenerator->generate('app_tipico_simulation_statistic', ['simulator' => $simulator->getId()]),
            $simulator->getIdentifier(),
            $simulator->getCashBox(),
            $total,
            $average
        );
    }

    public function getHtml(): string
    {
        $bet = $this->betPlacementSuggestion->getSuggestedBet();

        $cssClasses = $this->calculateOddMatrix(
            $this->betPlacementSuggestion->getPlacementSuggestions()[0]->getSimulator(),
            false,
            true
        );
        foreach ($this->betPlacementSuggestion->getPlacementSuggestions() as $placementSuggestion) {
            // Calculate matrix
            $matrix = $this->calculateOddMatrix($placementSuggestion->getSimulator(), false, true);

            foreach ($matrix as $betVariant => $values) {
                foreach ($values as $key => $value) {
                    $currentValue = $matrix[$betVariant][$key];
                    if (str_contains($currentValue, 'is-target')) {
                        $cssClasses[$betVariant][$key] = $currentValue;
                    }
                }
            }
        }
//        dd($cssClasses);


        $mapped = [];
        $current = new DateTime();

        $start = (new DateTime())->setTimestamp($bet->getStartAtTimeStamp() / 1000);
        $timeDistance = SimulationStatisticService::getTimeDistance($start, $current);

        $expectedEnd = (new DateTime())->setTimestamp($bet->getStartAtTimeStamp() / 1000);
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
        if ($bet->isFinished()) {
            $classFinished = 'finished';
        }

        $matchName = $bet->getHomeTeamName() . ' - ' . $bet->getAwayTeamName();
        $url = '<a href="https://www.google.com/search?q=' . $matchName . ' ' . $start->format(
                'd.m.y H:i'
            ) . ' result" target="_blank">Google</a>';
        $tipicoUrl = '<a href=https://sports.tipico.de/de/heute/default/event/' . $bet->getTipicoId(
            ) . ' result" target="_blank">Tipico</a>';
        $betanoUrl = '<a href="https://www.betano.de/" class="external-link" target="_blank" data-copy-text="' . $matchName . '">Betano</a>';


//        $cssClasses = [
//            'CLASSIC_3_WAY' => [
//                0 => 'home-class',  // CSS class for home team odds
//                1 => 'draw-class',  // CSS class for draw odds
//                2 => 'away-class'   // CSS class for away team odds
//            ],
//            'OVER_UNDER' => [
//                0 => 'over-class-1', 1 => 'under-class-1',  // CSS classes for over/under odds row 1
//                2 => 'over-class-2', 3 => 'under-class-2',  // CSS classes for over/under odds row 2
//                4 => 'over-class-3', 5 => 'under-class-3',   // CSS classes for over/under odds row 3
//                6 => 'over-class-3', 7 => 'under-class-3',   // CSS classes for over/under odds row 3
//                8 => 'over-class-3', 9 => 'under-class-3',   // CSS classes for over/under odds row 3
//                10 => 'over-class-3', 11 => 'under-class-3',   // CSS classes for over/under odds row 3
//            ],
//            'BOTH_TEAMS_SCORE' => [
//                0 => 'both-teams-score-yes-class',  // CSS class for both teams to score (yes)
//                1 => 'both-teams-score-no-class'    // CSS class for both teams to score (no)
//            ],
//            'HEAD_TO_HEAD' => [
//                0 => 'head-to-head-home-class',  // CSS class for head-to-head home team
//                1 => 'head-to-head-away-class'   // CSS class for head-to-head away team
//            ]
//        ];
//        $cssClasses = $this->calculateOddMatrix();


        $mapped = sprintf(
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
                                    <span class="tipico-url">%s</span>
                                    <span>%s</span>
                                </span>
                                <span class="check-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="50" height="50">
                                        <path fill="#000000" d="M9.293 16.707l-5-5 1.414-1.414 3.586 3.586 8.293-8.293 1.414 1.414-9 9z"/>
                                    </svg>
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
            $bet->getHomeTeamName(),
            $bet->getAwayTeamName(),
            $classFinished,
            $homeGoals,
            $awayGoals,
            $this->getThreeWayOdds($bet, $cssClasses),
            $url,
            $tipicoUrl,
            $betanoUrl,
            $this->getOverUnderOdds($bet, $cssClasses),
            $this->getBothTeamsScoreOdds($bet, $cssClasses),
            $this->getHeadToHeadOdds($bet, $cssClasses),

        );

        return $mapped;
    }
}
