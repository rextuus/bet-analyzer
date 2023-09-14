<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Api\Response;

use App\Service\Sportmonks\Api\Event\ApiRoute;
use App\Service\Sportmonks\Api\ResponseCanTriggerNextMessageInterface;
use App\Service\Sportmonks\Content\Standing\Data\SpmStandingData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class StandingResponse implements ResponseCanTriggerNextMessageInterface
{
    /**
     * @var SpmStandingData[]
     */
    private array $standings;

    private int|null $nextRoundApiId;

    private int|null $waitToContinue;

    public function __construct(array $standings, ?int $waitToContinue = null)
    {
        $this->standings = $standings;
        $this->waitToContinue = $waitToContinue;
    }

    public function getStandings(): array
    {
        return $this->standings;
    }

    public function setStandings(array $standings): StandingResponse
    {
        $this->standings = $standings;
        return $this;
    }

    public function getWaitToContinue(): ?int
    {
        return $this->waitToContinue;
    }

    public function setWaitToContinue(?int $waitToContinue): StandingResponse
    {
        $this->waitToContinue = $waitToContinue;
        return $this;
    }

    public function getNextRoundApiId(): ?int
    {
        return $this->nextRoundApiId;
    }

    public function setNextRoundApiId(?int $nextRoundApiId): StandingResponse
    {
        $this->nextRoundApiId = $nextRoundApiId;
        return $this;
    }

    public function getApiRoute(): ApiRoute
    {
        return ApiRoute::STANDING;
    }

    public function getMessageParameter(): ?int
    {
        return $this->getNextRoundApiId();
    }

    public function setMessageParameter(int $parameter): void
    {
        $this->setNextRoundApiId($parameter);
    }
}
