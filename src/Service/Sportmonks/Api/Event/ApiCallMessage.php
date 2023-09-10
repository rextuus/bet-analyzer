<?php

namespace App\Service\Sportmonks\Api\Event;

final class ApiCallMessage
{
    private int $page;
    private int $fixtureId;

    public function __construct(
        private readonly ApiRoute $apiRoute,
    ) {
    }

    public function getApiRoute(): ApiRoute
    {
        return $this->apiRoute;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): ApiCallMessage
    {
        $this->page = $page;
        return $this;
    }

    public function getFixtureId(): int
    {
        return $this->fixtureId;
    }

    public function setFixtureId(int $fixtureId): ApiCallMessage
    {
        $this->fixtureId = $fixtureId;
        return $this;
    }
}
