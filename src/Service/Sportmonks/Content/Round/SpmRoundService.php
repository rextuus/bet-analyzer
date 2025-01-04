<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Round;

use App\Entity\Spm\SpmRound;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class SpmRoundService
{
    public function __construct(private SpmRoundRepository $repository, private SpmRoundFactory $factory)
    {
    }

    public function createByData(SpmRoundData $data): SpmRound
    {
        $spmRound = $this->factory->createByData($data);
        $this->repository->save($spmRound);
        return $spmRound;
    }

    public function update(SpmRound $spmRound, SpmRoundData $data): SpmRound
    {
        $spmRound = $this->factory->mapData($data, $spmRound);
        $this->repository->save($spmRound);
        return $spmRound;
    }

    /**
     * @return SpmRound[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findById(int $apiId): ?SpmRound
    {
        return $this->repository->findOneBy(['apiId' => $apiId]);
    }

    /**
     * @return SpmRound[]
     */
    public function findRoundWithoutStandings(): array
    {
        return $this->repository->findRoundWithoutStandings();
    }

    public function findRoundsBySeason(?int $roundApiId): array
    {
        return $this->repository->findBy(['seasonApiId' => $roundApiId]);
    }
}
