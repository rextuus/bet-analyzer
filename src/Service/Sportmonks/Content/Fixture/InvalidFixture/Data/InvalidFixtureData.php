<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture\InvalidFixture\Data;

use App\Entity\InvalidFixture;
use DateTimeInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class InvalidFixtureData
{
    private int $fixtureApiId;
    private DateTimeInterface $decorationAttempt;
    private string $reason;

    public function getFixtureApiId(): int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): InvalidFixtureData
    {
        $this->fixtureApiId = $fixtureApiId;
        return $this;
    }

    public function getDecorationAttempt(): DateTimeInterface
    {
        return $this->decorationAttempt;
    }

    public function setDecorationAttempt(DateTimeInterface $decorationAttempt): InvalidFixtureData
    {
        $this->decorationAttempt = $decorationAttempt;
        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): InvalidFixtureData
    {
        $this->reason = $reason;
        return $this;
    }

    public function initFromEntity(InvalidFixture $invalidFixture): InvalidFixtureData
    {
        $this->setReason($invalidFixture->getReason());
        $this->setFixtureApiId($invalidFixture->getFixtureApiId());
        $this->setDecorationAttempt($invalidFixture->getDecorationAttempt());
        return $this;
    }
}
