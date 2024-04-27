<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile\Content;

use App\Entity\BettingProvider\BettingProviderBackupFile;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\Data\BettingProviderBackupFileData;


class BettingProviderBackupFileFactory
{
    public function createByData(BettingProviderBackupFileData $data): BettingProviderBackupFile
    {
        $bettingProviderBackupFile = $this->createNewInstance();
        $this->mapData($data, $bettingProviderBackupFile);
        return $bettingProviderBackupFile;
    }

    public function mapData(
        BettingProviderBackupFileData $data,
        BettingProviderBackupFile $bettingProviderBackupFile
    ): BettingProviderBackupFile {
        $bettingProviderBackupFile->setProvider($data->getProvider());
        $bettingProviderBackupFile->setFilePath($data->getFilePath());
        $bettingProviderBackupFile->setFittedBets($data->getFittedBets());
        $bettingProviderBackupFile->setContainingBets($data->getContainingBets());
        $bettingProviderBackupFile->setNonFittedBets($data->getNonFittedBets());
        $bettingProviderBackupFile->setAlreadyFittedBets($data->getAlreadyFittedBets());
        $bettingProviderBackupFile->setCreated($data->getCreated());
        $bettingProviderBackupFile->setIsConsumed($data->isConsumed());

        return $bettingProviderBackupFile;
    }

    private function createNewInstance(): BettingProviderBackupFile
    {
        return new BettingProviderBackupFile();
    }
}
