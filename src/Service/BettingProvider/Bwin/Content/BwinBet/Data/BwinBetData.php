<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Bwin\Content\BwinBet\Data;

use App\Entity\BettingProvider\TipicoBet;


class BwinBetData
{
    private string $bwinId;
    private string $homeTeamName;
    private string $awayTeamName;
    private int $startAtTimeStamp;
    private float $oddHome = 0.0;
    private float $oddDraw = 0.0;
    private float $oddAway = 0.0;
    private string $url = '-';
    private int $sportRadarId = -1;

    private TipicoBet $tipicoBet;

    public function getBwinId(): string
    {
        return $this->bwinId;
    }

    public function setBwinId(string $bwinId): BwinBetData
    {
        $this->bwinId = $bwinId;
        return $this;
    }

    public function getHomeTeamName(): string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(string $homeTeamName): BwinBetData
    {
        $this->homeTeamName = $homeTeamName;
        return $this;
    }

    public function getAwayTeamName(): string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(string $awayTeamName): BwinBetData
    {
        $this->awayTeamName = $awayTeamName;
        return $this;
    }

    public function getStartAtTimeStamp(): int
    {
        return $this->startAtTimeStamp;
    }

    public function setStartAtTimeStamp(int $startAtTimeStamp): BwinBetData
    {
        $this->startAtTimeStamp = $startAtTimeStamp;
        return $this;
    }

    public function getOddHome(): float
    {
        return $this->oddHome;
    }

    public function setOddHome(float $oddHome): BwinBetData
    {
        $this->oddHome = $oddHome;
        return $this;
    }

    public function getOddDraw(): float
    {
        return $this->oddDraw;
    }

    public function setOddDraw(float $oddDraw): BwinBetData
    {
        $this->oddDraw = $oddDraw;
        return $this;
    }

    public function getOddAway(): float
    {
        return $this->oddAway;
    }

    public function setOddAway(float $oddAway): BwinBetData
    {
        $this->oddAway = $oddAway;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): BwinBetData
    {
        $this->url = $url;
        return $this;
    }

    public function getSportRadarId(): int
    {
        return $this->sportRadarId;
    }

    public function setSportRadarId(int $sportRadarId): BwinBetData
    {
        $this->sportRadarId = $sportRadarId;
        return $this;
    }

    public function getTipicoBet(): TipicoBet
    {
        return $this->tipicoBet;
    }

    public function setTipicoBet(TipicoBet $tipicoBet): BwinBetData
    {
        $this->tipicoBet = $tipicoBet;
        return $this;
    }
}
