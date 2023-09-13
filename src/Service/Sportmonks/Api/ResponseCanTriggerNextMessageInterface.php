<?php

namespace App\Service\Sportmonks\Api;

use App\Service\Sportmonks\Api\Event\ApiRoute;

interface ResponseCanTriggerNextMessageInterface
{
    public function getApiRoute(): ApiRoute;
    public function getMessageParameter(): ?int;
    public function setMessageParameter(int $parameter): void;
    public function getWaitToContinue(): ?int;
}
