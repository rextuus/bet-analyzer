<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Response;

use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Api\ResponseCanTriggerNextMessageInterface;
use App\Service\Sportmonks\Content\Odd\Data\SpmOddData;
use App\Service\Sportmonks\Content\Score\Data\SpmScoreData;


class OddAndScoreResponse implements ResponseCanTriggerNextMessageInterface
{
    /**
     * @var SpmOddData[]
     */
    private array $odds;

    /**
     * @var SpmScoreData[]
     */
    private array $scores;

    private int|null $nextFixture;

    private int|null $waitToContinue;

    public function __construct(array $odds, array $scores, ?int $waitToContinue = null)
    {
        $this->odds = $odds;
        $this->waitToContinue = $waitToContinue;
        $this->scores = $scores;
    }

    public function getOdds(): array
    {
        return $this->odds;
    }

    public function setOdds(array $odds): OddAndScoreResponse
    {
        $this->odds = $odds;
        return $this;
    }

    public function getScores(): array
    {
        return $this->scores;
    }

    public function setScores(array $scores): OddAndScoreResponse
    {
        $this->scores = $scores;
        return $this;
    }

    public function getNextFixture(): ?int
    {
        return $this->nextFixture;
    }

    public function setNextFixture(?int $nextFixture): OddAndScoreResponse
    {
        $this->nextFixture = $nextFixture;
        return $this;
    }

    public function getWaitToContinue(): ?int
    {
        return $this->waitToContinue;
    }

    public function setWaitToContinue(?int $waitToContinue): OddAndScoreResponse
    {
        $this->waitToContinue = $waitToContinue;
        return $this;
    }

    public function getApiRoute(): ApiRoute
    {
        return ApiRoute::ODD;
    }

    public function getMessageParameter(): ?int
    {
        return $this->getNextFixture();
    }

    public function setMessageParameter(int $parameter): void
    {
        $this->setNextFixture($parameter);
    }
}
