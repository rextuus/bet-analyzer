<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile\Content;

use App\Entity\BettingProviderBackupFile;
use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\Data\BettingProviderBackupFileData;
use App\Service\BettingProvider\BettingProviderBackupFile\Message\StoreBetsForProviderMessage;
use DateTime;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BettingProviderBackupFileService
{
    public function __construct(
        private readonly BettingProviderBackupFileRepository $repository,
        private readonly BettingProviderBackupFileFactory $factory,
        private readonly KernelInterface $kernel,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function createByData(BettingProviderBackupFileData $data): BettingProviderBackupFile
    {
        $bettingProviderBackupFile = $this->factory->createByData($data);
        $this->repository->save($bettingProviderBackupFile);
        return $bettingProviderBackupFile;
    }

    public function update(
        BettingProviderBackupFile $bettingProviderBackupFile,
        BettingProviderBackupFileData $data
    ): BettingProviderBackupFile {
        $bettingProviderBackupFile = $this->factory->mapData($data, $bettingProviderBackupFile);
        $this->repository->save($bettingProviderBackupFile);
        return $bettingProviderBackupFile;
    }

    /**
     * @return BettingProviderBackupFile[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function find(int $id): ?BettingProviderBackupFile
    {
        return $this->repository->find($id);
    }

    public function storeNewBackupRelation(BettingProvider $provider, Request $request): void
    {
        $backupFilePath = $this->storeJsonBackupFile($provider, $request);

        // store backup entity
        $data = new BettingProviderBackupFileData();
        $data->setProvider($provider);

        $data->setContainingBets(0);
        $data->setFittedBets(0);
        $data->setNonFittedBets(0);
        $data->setAlreadyFittedBets(0);

        $data->setCreated(new DateTime());
        $data->setIsConsumed(false);
        $data->setFilePath($backupFilePath);
        $betanoBackup = $this->createByData($data);

        // dispatch event
        $message = new StoreBetsForProviderMessage($betanoBackup->getId(), $provider);
        $this->messageBus->dispatch($message);
    }

    private function storeJsonBackupFile(BettingProvider $bettingProvider, Request $request): string
    {
        $currentDateTime = new DateTime();

        $basePath = $this->kernel->getProjectDir() . '/public/backups/';
        match ($bettingProvider) {
            BettingProvider::BETANO => $backupDir = $basePath . 'betano/' . $currentDateTime->format('m_d_Y'),
            BettingProvider::BWIN => $backupDir = $basePath . 'bwin',
            BettingProvider::TIPICO => throw new Exception('Not supported'),
        };

        $timestamp = $currentDateTime->format('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/' . $timestamp . '.json';

        $filesystem = new Filesystem();

        if (!$filesystem->exists($backupDir)) {
            $filesystem->mkdir($backupDir, 0755);
        }

        $filesystem->touch($backupFile);
        $filesystem->appendToFile($backupFile, $request->getContent());

        return $backupFile;
    }

    /**
     * @return array<string, mixed>
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
