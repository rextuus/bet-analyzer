<?php

namespace App\Twig\Components;

use App\Entity\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\SimulationStatisticService;
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

    public BetOn $betOn;

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
            if ($current > $start){
                $startedClass = 'started';

                if ($expectedEnd > $current){
                    $startedClass = 'running';
                }
            }

            $homeClass = $this->betOn === BetOn::HOME ? 'target' : 'non-target';
            $drawClass = $this->betOn === BetOn::DRAW ? 'target' : 'non-target';
            $awayClass = $this->betOn === BetOn::AWAY ? 'target' : 'non-target';

            $homeGoals = 0;
            $awayGoals = 0;
            $classFinished = 'non-finished';
            $classSimulatorWon = 'non-simulator-won';
            if ($fixture->isFinished()){
                $homeGoals = $fixture->getEndScoreHome();
                $awayGoals = $fixture->getEndScoreAway();
                $result = BetOn::DRAW;
                if ($homeGoals > $awayGoals){
                    $result = BetOn::HOME;
                }
                if ($awayGoals > $homeGoals){
                    $result = BetOn::AWAY;
                }

                if($result === $this->betOn){
                    $classSimulatorWon = 'simulator-won';
                }

                $classFinished = 'finished';
            }

            $url = '<a href="https://www.google.com/search?q='.$fixture->getHomeTeamName().' - '.$fixture->getAwayTeamName().' '.$start->format('d.m.y H:i').' result" target="_blank">Google</a>';
            $tipicoUrl = '<a href=https://sports.tipico.de/de/heute/default/event/'.$fixture->getTipicoId().' result" target="_blank">Tipico</a>';

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
                            <span class="%s">%.2f</span> 
                            <span class="%s">%.2f</span> 
                            <span class="%s">%.2f</span>
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
                $homeClass,
                $fixture->getOddHome(),
                $drawClass,
                $fixture->getOddDraw(),
                $awayClass,
                $fixture->getOddAway(),
                $url,
                $tipicoUrl
            );
        }

        return $mapped;
    }
}
