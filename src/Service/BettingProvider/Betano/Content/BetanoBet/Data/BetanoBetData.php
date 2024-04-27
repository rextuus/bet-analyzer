<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Content\BetanoBet\Data;

use App\Entity\BettingProvider\TipicoBet;


class BetanoBetData
{
    private int $betanoId;
    private string $homeTeamName;
    private string $awayTeamName;
    private int $startAtTimeStamp;
    private float $oddHome = 0.0;
    private float $oddDraw = 0.0;
    private float $oddAway = 0.0;
    private string $url = '-';
    private int $sportRadarId = -1;

    private TipicoBet $tipicoBet;

    public function getBetanoId(): int
    {
        return $this->betanoId;
    }

    public function setBetanoId(int $betanoId): BetanoBetData
    {
        $this->betanoId = $betanoId;
        return $this;
    }

    public function getHomeTeamName(): string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(string $homeTeamName): BetanoBetData
    {
        $this->homeTeamName = $homeTeamName;
        return $this;
    }

    public function getAwayTeamName(): string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(string $awayTeamName): BetanoBetData
    {
        $this->awayTeamName = $awayTeamName;
        return $this;
    }

    public function getStartAtTimeStamp(): int
    {
        return $this->startAtTimeStamp;
    }

    public function setStartAtTimeStamp(int $startAtTimeStamp): BetanoBetData
    {
        $this->startAtTimeStamp = $startAtTimeStamp;
        return $this;
    }

    public function getOddHome(): float
    {
        return $this->oddHome;
    }

    public function setOddHome(float $oddHome): BetanoBetData
    {
        $this->oddHome = $oddHome;
        return $this;
    }

    public function getOddDraw(): float
    {
        return $this->oddDraw;
    }

    public function setOddDraw(float $oddDraw): BetanoBetData
    {
        $this->oddDraw = $oddDraw;
        return $this;
    }

    public function getOddAway(): float
    {
        return $this->oddAway;
    }

    public function setOddAway(float $oddAway): BetanoBetData
    {
        $this->oddAway = $oddAway;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): BetanoBetData
    {
        $this->url = $url;
        return $this;
    }

    public function getSportRadarId(): int
    {
        return $this->sportRadarId;
    }

    public function setSportRadarId(int $sportRadarId): BetanoBetData
    {
        $this->sportRadarId = $sportRadarId;
        return $this;
    }

    public function getTipicoBet(): TipicoBet
    {
        return $this->tipicoBet;
    }

    public function setTipicoBet(TipicoBet $tipicoBet): BetanoBetData
    {
        $this->tipicoBet = $tipicoBet;
        return $this;
    }
}
