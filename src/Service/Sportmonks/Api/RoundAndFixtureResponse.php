<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api;

use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class RoundAndFixtureResponse
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

    public function getWaitToContinue(): ?int
    {
        return $this->waitToContinue;
    }
}
