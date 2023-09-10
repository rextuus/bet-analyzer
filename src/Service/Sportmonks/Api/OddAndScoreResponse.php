<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api;

use App\Service\Sportmonks\Content\Odd\Data\SpmOddData;
use App\Service\Sportmonks\Content\Score\Data\SpmScoreData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class OddAndScoreResponse
{
    /**
     * @var SpmOddData[]
     */
    private array $odds;

    /**
     * @var SpmScoreData[]
     */
    private array $scores;

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

    public function getWaitToContinue(): ?int
    {
        return $this->waitToContinue;
    }

    public function setWaitToContinue(?int $waitToContinue): OddAndScoreResponse
    {
        $this->waitToContinue = $waitToContinue;
        return $this;
    }
}
