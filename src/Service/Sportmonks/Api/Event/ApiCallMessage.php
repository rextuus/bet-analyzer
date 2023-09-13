<?php

namespace App\Service\Sportmonks\Api\Event;

final class ApiCallMessage
{
    private int $messageParameter;

    public function __construct(
        private readonly ApiRoute $apiRoute,
    ) {
    }

    public function getApiRoute(): ApiRoute
    {
        return $this->apiRoute;
    }

    public function getMessageParameter(): int
    {
        return $this->messageParameter;
    }

    public function setMessageParameter(int $messageParameter): ApiCallMessage
    {
        $this->messageParameter = $messageParameter;
        return $this;
    }
}
