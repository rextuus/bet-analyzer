<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
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
