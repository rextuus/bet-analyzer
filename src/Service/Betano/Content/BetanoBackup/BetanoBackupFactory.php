<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoBackup;

use App\Entity\BetanoBackup;
use App\Service\Betano\Content\BetanoBackup\Data\BetanoBackupData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoBackupFactory
{
    public function createByData(BetanoBackupData $data): BetanoBackup
    {
        $betanoBackup = $this->createNewInstance();
        $this->mapData($data, $betanoBackup);
        return $betanoBackup;
    }

    public function mapData(BetanoBackupData $data, BetanoBackup $betanoBackup): BetanoBackup
    {
        $betanoBackup->setCreated($data->getCreated());
        $betanoBackup->setFilePath($data->getFilePath());
        $betanoBackup->setFittedBets($data->getFittedBets());
        $betanoBackup->setNonFittedBets($data->getNonFittedBets());
        $betanoBackup->setContainedBets($data->getContainedBets());
        $betanoBackup->setAlreadyStoredBets($data->getAlreadyStoredBets());
        $betanoBackup->setFilePath($data->getFilePath());
        $betanoBackup->setIsConsumed($data->isConsumed());

        return $betanoBackup;
    }

    private function createNewInstance(): BetanoBackup
    {
        return new BetanoBackup();
    }
}
