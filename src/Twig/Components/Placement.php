<?php

namespace App\Twig\Components;

use App\Entity\TipicoPlacement;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Placement
{
    public TipicoPlacement $placement;
    public float $currentCashBox;

    public function getRowClass(): string
    {
        if ($this->placement->isWon()){
            return 'increase';
        }

        return 'decrease';
    }

    public function getOutput(): float
    {
        $input = 0.0;
        if ($this->placement->isWon()){
            $input = $this->placement->getInput();
        }

        $value = $this->placement->getValue() * $input - $this->placement->getInput();


        return $value;
    }

    public function getIconPath(): string
    {
        if ($this->placement->isWon()){
            return '<path fill="#415d35" d="M12 4.166l-7.07 7.07 1.414 1.414L12 6.994l5.656 5.656 1.414-1.414z"/>';
        }

        return '<path fill="#FF1744" d="M12 19.834l7.07-7.07-1.414-1.414L12 17.666 6.344 11.01 4.93 12.424z"/>';
    }

    public function getHomeClass(): string
    {
        $fixture = $this->placement->getFixtures()->toArray()[0];
        if ($fixture->getEndScoreHome() > $fixture->getEndScoreAway()){
            return 'winner-team';
        }

        return '';
    }

    public function getAwayClass(): string
    {
        $fixture = $this->placement->getFixtures()->toArray()[0];
        if ($fixture->getEndScoreHome() < $fixture->getEndScoreAway()){
            return 'winner-team';
        }

        return '';
    }

    public function getDrawClass(): string
    {
        $fixture = $this->placement->getFixtures()->toArray()[0];
        if ($fixture->getEndScoreHome() === $fixture->getEndScoreAway()){
            return 'winner-team';
        }

        return '';
    }

    public function getNewCashBox(): float
    {
        return $this->currentCashBox + $this->getOutput();
    }

    public function getAssetPath(): string
    {
        $fixture = $this->placement->getFixtures()->toArray()[0];
        if ($fixture->getEndScoreHome() > $fixture->getEndScoreAway()){
            return 'asset/stadium.svg';
        }
        if ($fixture->getEndScoreHome() < $fixture->getEndScoreAway()){
            return 'asset/train.svg';
        }
        return 'asset/chess.svg';
    }

    public function getOverOdd(): string
    {
        $ident = $this->placement->getSimulator()->getIdentifier();
        $parts = explode('_', $ident);
        $use = $parts[array_key_last($parts)];
        $overUnderOdds = $this->placement->getFixtures()->get(0)->getTipicoOverUnderOdds();
        if($overUnderOdds->count()){
            return $overUnderOdds->get($use)->getOverValue();
        }
        return '-';
    }

    public function getUnderOdd(): string
    {
        $ident = $this->placement->getSimulator()->getIdentifier();
        $parts = explode('_', $ident);
        $use = $parts[array_key_last($parts)];
        $overUnderOdds = $this->placement->getFixtures()->get(0)->getTipicoOverUnderOdds();
        if($overUnderOdds->count()){
            return $overUnderOdds->get($use)->getUnderValue();
        }
        return '-';
    }

    public function getBothTeamsScoreOdd(): string
    {
        $bothTeamsScoreOdd = $this->placement->getFixtures()->get(0)->getTipicoBothTeamsScoreBet();
        if($bothTeamsScoreOdd){
            return $bothTeamsScoreOdd->getConditionTrueValue();
        }
        return '-';
    }

    public function getBothTeamsScoreNotOdd(): string
    {
        $bothTeamsScoreOdd = $this->placement->getFixtures()->get(0)->getTipicoBothTeamsScoreBet();
        if($bothTeamsScoreOdd){
            return $bothTeamsScoreOdd->getConditionFalseValue();
        }
        return '-';
    }
}
