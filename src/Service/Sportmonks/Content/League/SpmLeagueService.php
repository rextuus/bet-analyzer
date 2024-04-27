<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\League;

use App\Entity\Spm\SpmLeague;
use App\Service\Sportmonks\Content\League\Data\SpmLeagueData;


class SpmLeagueService
{
    public function __construct(private SpmLeagueRepository $repository, private SpmLeagueFactory $factory)
    {
    }

    public function createByData(SpmLeagueData $data): SpmLeague
    {
        $spmLeague = $this->factory->createByData($data);
        $this->repository->save($spmLeague);
        return $spmLeague;
    }

    public function update(SpmLeague $spmLeague, SpmLeagueData $data): SpmLeague
    {
        $spmLeague = $this->factory->mapData($data, $spmLeague);
        $this->repository->save($spmLeague);
        return $spmLeague;
    }

    /**
     * @return SpmLeague[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findById(int $apiId): ?SpmLeague
    {
        return $this->repository->findOneBy(['apiId' => $apiId]);
    }
}
