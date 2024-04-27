<?php

declare(strict_types=1);

namespace App\Service\BettingProvider;

use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\Data\BettingProviderBackupFileData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
abstract class AbstractBettingProviderService
{
    public function __construct(
        private readonly TipicoBetService $tipicoBetService,
        private readonly BettingProviderBackupFileService $bettingProviderBackupFileService,
    ) {
    }

    public function storeBetsFromBackupFile(int $backupId): void
    {
        $backup = $this->bettingProviderBackupFileService->find($backupId);
        $data = (new BettingProviderBackupFileData())->initFromEntity($backup);

        $content = $this->readInBackupFile($backup->getFilePath());

        $response = $this->createResponseToParse($content);
        $response->parseResponse();

        $idIdent = $this->getProviderIdent();
        $idGetter = $this->getIdGetter();

        $dataObjects = $response->getDataObjects();
        $counter = ['total' => count($dataObjects), 'existing' => 0, 'skipped' => 0, 'added' => 0];
        foreach ($dataObjects as $betData) {
            // skip existing ones
            if (count($this->findBy([$idIdent => $betData->{$idGetter}()]))) {
                $counter['existing']++;
                continue;
            }

            // skip if betano provided no sportRadar id
            if ($betData->getSportRadarId() === -1) {
                $counter['skipped']++;
                continue;
            }

            // throw bets in trash we cant find a tipico bet for or we find multiple tipico bets for
            $tipicoBets = $this->tipicoBetService->findBy(['sportRadarId' => $betData->getSportRadarId()]);
            if (count($tipicoBets) === 1) {
                $tipicoBet = $tipicoBets[0];
                $betData->setTipicoBet($tipicoBet);
                $this->createByData($betData);
                $counter['added']++;
            }
        }

        $data->setContainingBets($counter['total']);
        $data->setFittedBets($counter['added']);
        $data->setNonFittedBets($counter['skipped']);
        $data->setAlreadyFittedBets($counter['existing']);
        $data->setIsConsumed(true);

        $this->bettingProviderBackupFileService->update($backup, $data);
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function readInBackupFile(string $backupFilePath): array
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($backupFilePath)) {
            $jsonData = file_get_contents($backupFilePath);

            return json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        }

        throw new Exception(sprintf('Backup file %s not found', $backupFilePath));
    }

    private function getIdGetter(): string
    {
        return 'get' . ucfirst($this->getProviderIdent());
    }
}
