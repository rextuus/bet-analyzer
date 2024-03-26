<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoSettings;

use App\Entity\BetanoSettings;
use App\Service\Betano\Content\BetanoSettings\Data\BetanoSettingsData;
use App\Service\Betano\Content\BetanoSettings\Data\BetanoSettingsFactory;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoSettingsService
{
    private const DEFAULT_SETTINGS = 'default_settings';

    public function __construct(private readonly BetanoSettingsRepository $repository, private readonly BetanoSettingsFactory $factory)
    {
    }

    public function createByData(BetanoSettingsData $data): BetanoSettings
    {
        $betanoSettings = $this->factory->createByData($data);
        $this->repository->save($betanoSettings);
        return $betanoSettings;
    }

    public function update(BetanoSettings $betanoSettings, BetanoSettingsData $data): BetanoSettings
    {
        $betanoSettings = $this->factory->mapData($data, $betanoSettings);
        $this->repository->save($betanoSettings);
        return $betanoSettings;
    }

    /**
     * @return BetanoSettings[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function getDefaultSettings(): BetanoSettings
    {
        $result = $this->findBy(['identifier' => self::DEFAULT_SETTINGS]);
        if (count($result) === 0){
            $data = new BetanoSettingsData();
            $data->setCollectionEnabled(true);
            $data->setCollectionRunning(false);
            $data->setIdentifier(self::DEFAULT_SETTINGS);
            $data->setExpectedExecutionTime((new DateTime())->getTimestamp());

            return $this->createByData($data);
        }

        return $result[0];
    }

    public function setDefaultSettingsRunning(bool $isRunning): void
    {
        $settings = $this->getDefaultSettings();
        $data = (new BetanoSettingsData())->initFromEntity($settings);
        $data->setCollectionRunning($isRunning);

        $this->update($settings, $data);
    }

    public function setDefaultSettingsNextExecutionTime(int $executionTime): void
    {
        $settings = $this->getDefaultSettings();
        $data = (new BetanoSettingsData())->initFromEntity($settings);
        $data->setExpectedExecutionTime($executionTime);

        $this->update($settings, $data);
    }

}
