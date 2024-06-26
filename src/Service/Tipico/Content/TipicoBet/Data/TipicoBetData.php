<?php
declare(strict_types=1);
namespace App\Service\Tipico\Content\TipicoBet\Data;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;


class TipicoBetData
{
    private int $tipicoId;

    private int $tipicoHomeTeamId;

    private int $tipicoAwayTeamId;

    private string $homeTeamName;

    private string $awayTeamName;

    private int $startAtTimeStamp;

    private float $oddHome;

    private float $oddDraw;

    private float $oddAway;

    private int $endScoreHome;

    private int $endScoreAway;

    private bool $finished;

    private BetOn $result;

    private int $sportRadarId = -1;

    public function getTipicoId(): int
    {
        return $this->tipicoId;
    }

    public function setTipicoId(int $tipicoId): TipicoBetData
    {
        $this->tipicoId = $tipicoId;
        return $this;
    }

    public function getTipicoHomeTeamId(): int
    {
        return $this->tipicoHomeTeamId;
    }

    public function setTipicoHomeTeamId(int $tipicoHomeTeamId): TipicoBetData
    {
        $this->tipicoHomeTeamId = $tipicoHomeTeamId;
        return $this;
    }

    public function getTipicoAwayTeamId(): int
    {
        return $this->tipicoAwayTeamId;
    }

    public function setTipicoAwayTeamId(int $tipicoAwayTeamId): TipicoBetData
    {
        $this->tipicoAwayTeamId = $tipicoAwayTeamId;
        return $this;
    }

    public function getHomeTeamName(): string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(string $homeTeamName): TipicoBetData
    {
        $this->homeTeamName = $homeTeamName;
        return $this;
    }

    public function getAwayTeamName(): string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(string $awayTeamName): TipicoBetData
    {
        $this->awayTeamName = $awayTeamName;
        return $this;
    }

    public function getStartAtTimeStamp(): int
    {
        return $this->startAtTimeStamp;
    }

    public function setStartAtTimeStamp(int $startAtTimeStamp): TipicoBetData
    {
        $this->startAtTimeStamp = $startAtTimeStamp;
        return $this;
    }

    public function getOddHome(): float
    {
        return $this->oddHome;
    }

    public function setOddHome(float $oddHome): TipicoBetData
    {
        $this->oddHome = $oddHome;
        return $this;
    }

    public function getOddDraw(): float
    {
        return $this->oddDraw;
    }

    public function setOddDraw(float $oddDraw): TipicoBetData
    {
        $this->oddDraw = $oddDraw;
        return $this;
    }

    public function getOddAway(): float
    {
        return $this->oddAway;
    }

    public function setOddAway(float $oddAway): TipicoBetData
    {
        $this->oddAway = $oddAway;
        return $this;
    }

    public function getEndScoreHome(): int
    {
        return $this->endScoreHome;
    }

    public function setEndScoreHome(int $endScoreHome): TipicoBetData
    {
        $this->endScoreHome = $endScoreHome;
        return $this;
    }

    public function getEndScoreAway(): int
    {
        return $this->endScoreAway;
    }

    public function setEndScoreAway(int $endScoreAway): TipicoBetData
    {
        $this->endScoreAway = $endScoreAway;
        return $this;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): TipicoBetData
    {
        $this->finished = $finished;
        return $this;
    }

    public function getResult(): BetOn
    {
        return $this->result;
    }

    public function setResult(BetOn $result): TipicoBetData
    {
        $this->result = $result;
        return $this;
    }

    public function getSportRadarId(): int
    {
        return $this->sportRadarId;
    }

    public function setSportRadarId(int $sportRadarId): TipicoBetData
    {
        $this->sportRadarId = $sportRadarId;
        return $this;
    }

    public function initFromEntity(TipicoBet $bet): TipicoBetData
    {
        $this->setTipicoId($bet->getTipicoId());
        $this->setTipicoHomeTeamId($bet->getTipicoHomeTeamId());
        $this->setTipicoAwayTeamId($bet->getTipicoAwayTeamId());
        $this->setHomeTeamName($bet->getHomeTeamName());
        $this->setAwayTeamName($bet->getAwayTeamName());
        $this->setStartAtTimeStamp($bet->getStartAtTimeStamp());
        $this->setOddHome($bet->getOddHome());
        $this->setOddDraw($bet->getOddDraw());
        $this->setOddAway($bet->getOddAway());
        $this->setEndScoreHome($bet->getEndScoreHome());
        $this->setEndScoreAway($bet->getEndScoreAway());
        $this->setFinished($bet->isFinished());
        $this->setResult($bet->getResult());
        $this->setSportRadarId($bet->getSportRadarId() ?: -1);

        return $this;
    }

    public function initFromApiResponse(array $apiResponse): TipicoBetData
    {
        $this->setTipicoId($apiResponse['tipicoId']);
        $this->setTipicoHomeTeamId($apiResponse['tipicoHomeTeamId']);
        $this->setTipicoAwayTeamId($apiResponse['tipicoAwayTeamId']);
        $this->setHomeTeamName($apiResponse['homeTeamName']);
        $this->setAwayTeamName($apiResponse['awayTeamName']);
        $this->setStartAtTimeStamp($apiResponse['startAtTimeStamp']);
        $this->setOddHome($apiResponse['oddHome']);
        $this->setOddDraw($apiResponse['oddDraw']);
        $this->setOddAway($apiResponse['oddAway']);
        $this->setEndScoreHome($apiResponse['endScoreHome']);
        $this->setEndScoreAway($apiResponse['endScoreAway']);
        $this->setFinished($apiResponse['finished']);
        $this->setResult(BetOn::from($apiResponse['result']));
        $this->setSportRadarId(array_key_exists('sportRadarId', $apiResponse) ? $apiResponse['sportRadarId'] : -1);

        return $this;
    }
}
