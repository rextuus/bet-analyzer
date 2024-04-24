<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoBet;

use App\Entity\BetanoBet;
use App\Service\Betano\Api\Response\DailyMatchEventResponse;
use App\Service\Betano\Content\BetanoBackup\BetanoBackupService;
use App\Service\Betano\Content\BetanoBet\Data\BetanoBetData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use BetanoBackupData;
use Exception;
use JsonException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoBetService
{
    public function __construct(
        private readonly TipicoBetService $tipicoBetService,
        private readonly BetanoBackupService $betanoBackupService,
        private readonly BetanoBetRepository $repository,
        private readonly BetanoBetFactory $factory
    )
    {
    }

    public function createByData(BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->createByData($data);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    public function update(BetanoBet $betanoBet, BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->mapData($data, $betanoBet);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    /**
     * @return BetanoBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function storeBetanoBetsFromBackupFile(int $backupId): void
    {
        $backup = $this->betanoBackupService->find($backupId);
        $data = (new BetanoBackupData())->initFromEntity($backup);

        $content = $this->readInBackupFile($backup->getFilePath());

        $response = new DailyMatchEventResponse($content);
        $response->parseResponse();

        $dataObjects = $response->getBetanoBetDataObjects();
        $counter = ['total' => count($dataObjects), 'existing' => 0, 'skipped' => 0, 'added' => 0];
        foreach ($dataObjects as $betanoBetData) {
            // skip existing ones
            if (count($this->findBy(['betanoId' => $betanoBetData->getBetanoId()]))) {
                $counter['existing']++;
                continue;
            }

            // skip if betano provided no sportRadar id
            if ($betanoBetData->getBetanoId() === -1) {
                $counter['skipped']++;
                continue;
            }

            // throw bets in trash we cant find a tipico bet for or we find multiple tipico bets for
            $tipicoBets = $this->tipicoBetService->findBy(['sportRadarId' => $betanoBetData->getSportRadarId()]);
            if (count($tipicoBets) === 1) {
                $tipicoBet = $tipicoBets[0];
                $betanoBetData->setTipicoBet($tipicoBet);
                $this->createByData($betanoBetData);
                $counter['added']++;
            }
        }

        $data->setContainedBets($counter['total']);
        $data->setFittedBets($counter['added']);
        $data->setNonFittedBets($counter['skipped']);
        $data->setAlreadyStoredBets($counter['existing']);
        $data->setIsConsumed(true);

        $this->betanoBackupService->update($backup, $data);
    }

    /**
     * @return array<string, mixed>
     * @throws JsonException
     * @throws Exception
     */
    private function readInBackupFile(string $backupFilePath): array
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($backupFilePath)) {
            $jsonData = file_get_contents($backupFilePath);

            return json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        }

        throw new Exception(sprintf('Backup file %s not found', $backupFilePath));
    }
}
