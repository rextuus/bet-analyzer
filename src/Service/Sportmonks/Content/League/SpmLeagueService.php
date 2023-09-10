<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\League;

use App\Entity\SpmLeague;
use App\Service\Sportmonks\Content\League\Data\SpmLeagueData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
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
}
