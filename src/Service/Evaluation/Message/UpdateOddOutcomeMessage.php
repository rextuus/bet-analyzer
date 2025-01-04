<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class UpdateOddOutcomeMessage
{
    private array $fixtureIds;

    public function getFixtureIds(): array
    {
        return $this->fixtureIds;
    }

    public function setFixtureIds(array $fixtureIds): UpdateOddOutcomeMessage
    {
        $this->fixtureIds = $fixtureIds;
        return $this;
    }


}
