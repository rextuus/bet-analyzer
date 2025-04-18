<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture;

use App\Entity\Spm\SeasonStatistic;
use App\Entity\Spm\SpmFixture;
use App\Entity\Spm\SpmSeason;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class SpmFixtureService
{
    public function __construct(private SpmFixtureRepository $repository, private SpmFixtureFactory $factory)
    {
    }

    public function createByData(SpmFixtureData $data): SpmFixture
    {
        $spmFixture = $this->factory->createByData($data);
        $this->repository->save($spmFixture);
        return $spmFixture;
    }

    public function update(SpmFixture $spmFixture, SpmFixtureData $data): SpmFixture
    {
        $spmFixture = $this->factory->mapData($data, $spmFixture);
        $this->repository->save($spmFixture);
        return $spmFixture;
    }

    /**
     * @return SpmFixture[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @return SpmFixture[]
     */
    public function findBySeasonOrderedByTime(array $conditions): array
    {
        return $this->repository->findBy($conditions, ['startingAtTimestamp' => 'DESC']);
    }

    public function findByApiId(int $apiId): ?SpmFixture
    {
        return $this->repository->findOneBy(['apiId' => $apiId]);
    }

    public function findNextUndecoratedFixture()
    {
        return $this->repository->findNextUndecoratedFixture();
    }

    public function getFixtureWithOddDecorationBySeason(SeasonStatistic $seasonStatistic): int
    {
        return $this->repository->getFixtureWithOddDecorationBySeason($seasonStatistic);
    }

    public function findFixturesAndOddsBySeason(SpmSeason $season)
    {
        return $this->repository->findFixturesAndOddsBySeason($season);
    }
}
