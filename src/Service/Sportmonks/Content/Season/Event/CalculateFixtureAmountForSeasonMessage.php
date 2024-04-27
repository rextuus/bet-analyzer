<?php

namespace App\Service\Sportmonks\Content\Season\Event;

use App\Entity\Spm\SpmSeason;

final class CalculateFixtureAmountForSeasonMessage
{
    public function __construct(
        private SpmSeason $seasonApiId,
    ) {
    }

    public function getSeasonApiId(): SpmSeason
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(SpmSeason $seasonApiId): CalculateFixtureAmountForSeasonMessage
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }
}
