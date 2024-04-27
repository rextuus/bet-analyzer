<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season;

use App\Entity\Spm\SpmSeason;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;


class SpmSeasonService
{
    public function __construct(private readonly SpmSeasonRepository $repository, private readonly SpmSeasonFactory $factory)
    {
    }

    public function createByData(SpmSeasonData $data): SpmSeason
    {
        $spmSeason = $this->factory->createByData($data);
        $this->repository->save($spmSeason);
        return $spmSeason;
    }

    public function update(SpmSeason $spmSeason, SpmSeasonData $data): SpmSeason
    {
        $spmSeason = $this->factory->mapData($data, $spmSeason);
        $this->repository->save($spmSeason);
        return $spmSeason;
    }

    /**
     * @return SpmSeason[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function getSeasonFixtureAmountBasedOnStanding()
    {
        return $this->repository->getSeasonFixtureAmountBasedOnStanding();
    }

    public function findSeasonsWithoutStanding()
    {
        return $this->repository->findRoundWithoutStandings();
    }

    /**
     * @return SpmSeason[]
     */
    public function findApprovedSeasonsBetRows(bool $havePlacedBets = false): array
    {
        return $this->repository->findApprovedSeasonsBetRows($havePlacedBets);
    }
}
