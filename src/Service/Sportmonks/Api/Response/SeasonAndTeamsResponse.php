<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Response;

use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Api\ResponseCanTriggerNextMessageInterface;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;
use App\Service\Sportmonks\Content\Team\Data\SpmTeamData;


class SeasonAndTeamsResponse implements ResponseCanTriggerNextMessageInterface
{
    /**
     * @var SpmTeamData[]
     */
    private array $teams;

    /**
     * @var SpmSeasonData[]
     */
    private array $seasons;

    private int|null $nextPage;

    private int|null $waitToContinue;

    /**
     * @param SpmTeamData[] $teams
     * @param SpmSeasonData[] $seasons
     */
    public function __construct(array $teams, array $seasons, int $nextPage = null, int $waitToContinue = null)
    {
        $this->teams = $teams;
        $this->seasons = $seasons;
        $this->nextPage = $nextPage;
        $this->waitToContinue = $waitToContinue;
    }

    public function getTeams(): array
    {
        return $this->teams;
    }

    public function getSeasons(): array
    {
        return $this->seasons;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    public function setNextPage(?int $nextPage): SeasonAndTeamsResponse
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
        return ApiRoute::SEASON;
    }

    public function setMessageParameter(int $parameter): void
    {
        $this->setNextPage($parameter);
    }
}
