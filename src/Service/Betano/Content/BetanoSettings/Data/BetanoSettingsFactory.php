<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoSettings\Data;

use App\Entity\BetanoSettings;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoSettingsFactory
{
    public function createByData(BetanoSettingsData $data): BetanoSettings
    {
        $betanoSettings = $this->createNewInstance();
        $this->mapData($data, $betanoSettings);
        return $betanoSettings;
    }

    public function mapData(BetanoSettingsData $data, BetanoSettings $betanoSettings): BetanoSettings
    {
        $betanoSettings->setCollectionEnabled($data->isCollectionEnabled());
        $betanoSettings->setCollectionRunning($data->isCollectionRunning());
        $betanoSettings->setIdentifier($data->getIdentifier());
        $betanoSettings->setExpectedExecutionTime($data->getExpectedExecutionTime());

        return $betanoSettings;
    }

    private function createNewInstance(): BetanoSettings
    {
        return new BetanoSettings();
    }
}
