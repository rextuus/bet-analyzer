<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture\InvalidFixture;

use App\Entity\InvalidFixture;
use App\Service\Sportmonks\Content\Fixture\InvalidFixture\Data\InvalidFixtureData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class InvalidFixtureFactory
{
    public function createByData(InvalidFixtureData $data): InvalidFixture
    {
        $invalidFixture = $this->createNewInstance();
        $this->mapData($data, $invalidFixture);
        return $invalidFixture;
    }

    public function mapData(InvalidFixtureData $data, InvalidFixture $invalidFixture): InvalidFixture
    {
        $invalidFixture->setFixtureApiId($data->getFixtureApiId());
        $invalidFixture->setDecorationAttempt($data->getDecorationAttempt());
        $invalidFixture->setReason($data->getReason());

        return $invalidFixture;
    }

    private function createNewInstance(): InvalidFixture
    {
        return new InvalidFixture();
    }
}
