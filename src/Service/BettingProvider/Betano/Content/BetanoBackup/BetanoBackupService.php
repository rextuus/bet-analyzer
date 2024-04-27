<?php
declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Content\BetanoBackup;

use App\Entity\BetanoBackup;
use App\Service\BettingProvider\Betano\Content\BetanoBackup\Data\BetanoBackupData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoBackupService
{
    public function __construct(private readonly BetanoBackupRepository $repository, private readonly BetanoBackupFactory $factory)
    {
    }

    public function createByData(BetanoBackupData $data): BetanoBackup
    {
        $betanoBackup = $this->factory->createByData($data);
        $this->repository->save($betanoBackup);
        return $betanoBackup;
    }

    public function update(BetanoBackup $betanoBackup, BetanoBackupData $data): BetanoBackup
    {
        $betanoBackup = $this->factory->mapData($data, $betanoBackup);
        $this->repository->save($betanoBackup);
        return $betanoBackup;
    }

    /**
     * @return BetanoBackup[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function find(int $id): ?BetanoBackup
    {
        return $this->repository->find($id);
    }
}
