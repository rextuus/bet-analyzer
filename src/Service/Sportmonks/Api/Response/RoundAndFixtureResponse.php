<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Response;

use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Api\ResponseCanTriggerNextMessageInterface;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;


class RoundAndFixtureResponse implements ResponseCanTriggerNextMessageInterface
{
    /**
     * @var SpmRoundData[]
     */
    private array $rounds;

    /**
     * @var SpmFixtureData[]
     */
    private array $fixtures;

    private int|null $nextPage;

    private int|null $waitToContinue;

    /**
     * @param SpmRoundData[] $rounds
     * @param SpmFixtureData[] $fixtures
     */
    public function __construct(array $rounds, array $fixtures, int $nextPage = null, int $waitToContinue = null)
    {
        $this->rounds = $rounds;
        $this->fixtures = $fixtures;
        $this->nextPage = $nextPage;
        $this->waitToContinue = $waitToContinue;
    }

    public function getRounds(): array
    {
        return $this->rounds;
    }

    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    public function setNextPage(?int $nextPage): RoundAndFixtureResponse
    {
        $this->nextPage = $nextPage;
        return $this;
    }

    public function getMessageParameter(): ?int
    {
        return $this->getNextPage();
    }

    public function getWaitToContinue(): ?int
    {
        return $this->waitToContinue;
    }

    public function getApiRoute(): ApiRoute
    {
        return ApiRoute::ROUND;
    }

    public function setMessageParameter(int $parameter): void
    {
        $this->setNextPage($parameter);
    }
}
