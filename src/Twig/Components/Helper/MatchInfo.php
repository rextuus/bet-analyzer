<?php

declare(strict_types=1);

namespace App\Twig\Components\Helper;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class MatchInfo
{
    private int $homeGoals;
    private int $awayGoals;
    private string $classFinished;
    private string $classSimulatorWon;
    private string $url;
    private string $tipicoUrl;
    private string $betanoUrl;

    public function __construct(
        int $homeGoals,
        int $awayGoals,
        string $classFinished,
        string $classSimulatorWon,
        string $url,
        string $tipicoUrl,
        string $betanoUrl
    ) {
        $this->homeGoals = $homeGoals;
        $this->awayGoals = $awayGoals;
        $this->classFinished = $classFinished;
        $this->classSimulatorWon = $classSimulatorWon;
        $this->url = $url;
        $this->tipicoUrl = $tipicoUrl;
        $this->betanoUrl = $betanoUrl;
    }

    public function getHomeGoals(): int
    {
        return $this->homeGoals;
    }

    public function setHomeGoals(int $homeGoals): MatchInfo
    {
        $this->homeGoals = $homeGoals;
        return $this;
    }

    public function getAwayGoals(): int
    {
        return $this->awayGoals;
    }

    public function setAwayGoals(int $awayGoals): MatchInfo
    {
        $this->awayGoals = $awayGoals;
        return $this;
    }

    public function getClassFinished(): string
    {
        return $this->classFinished;
    }

    public function setClassFinished(string $classFinished): MatchInfo
    {
        $this->classFinished = $classFinished;
        return $this;
    }

    public function getClassSimulatorWon(): string
    {
        return $this->classSimulatorWon;
    }

    public function setClassSimulatorWon(string $classSimulatorWon): MatchInfo
    {
        $this->classSimulatorWon = $classSimulatorWon;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): MatchInfo
    {
        $this->url = $url;
        return $this;
    }

    public function getTipicoUrl(): string
    {
        return $this->tipicoUrl;
    }

    public function setTipicoUrl(string $tipicoUrl): MatchInfo
    {
        $this->tipicoUrl = $tipicoUrl;
        return $this;
    }

    public function getBetanoUrl(): string
    {
        return $this->betanoUrl;
    }

    public function setBetanoUrl(string $betanoUrl): MatchInfo
    {
        $this->betanoUrl = $betanoUrl;
        return $this;
    }
}
