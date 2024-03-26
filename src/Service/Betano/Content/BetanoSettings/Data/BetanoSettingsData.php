<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoSettings\Data;

use App\Entity\BetanoSettings;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoSettingsData
{
    private bool $collectionEnabled;

    private bool $collectionRunning;
    private string $identifier;
    private int $expectedExecutionTime;

    public function isCollectionEnabled(): bool
    {
        return $this->collectionEnabled;
    }

    public function setCollectionEnabled(bool $collectionEnabled): BetanoSettingsData
    {
        $this->collectionEnabled = $collectionEnabled;
        return $this;
    }

    public function isCollectionRunning(): bool
    {
        return $this->collectionRunning;
    }

    public function setCollectionRunning(bool $collectionRunning): BetanoSettingsData
    {
        $this->collectionRunning = $collectionRunning;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): BetanoSettingsData
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getExpectedExecutionTime(): int
    {
        return $this->expectedExecutionTime;
    }

    public function setExpectedExecutionTime(int $expectedExecutionTime): BetanoSettingsData
    {
        $this->expectedExecutionTime = $expectedExecutionTime;
        return $this;
    }

    public function initFromEntity(BetanoSettings $betanoSettings): BetanoSettingsData
    {
        $this->setCollectionRunning($betanoSettings->isCollectionRunning());
        $this->setCollectionEnabled($betanoSettings->isCollectionEnabled());
        $this->setIdentifier($betanoSettings->getIdentifier());
        $this->setExpectedExecutionTime($betanoSettings->getExpectedExecutionTime());

        return $this;
    }
}
