<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture;

use App\Entity\SeasonStatistic;
use App\Entity\SpmFixture;
use App\Service\Sportmonks\Content\Fixture\Data\SpmFixtureData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
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

    public function findNextUndecoratedFixture()
    {
        return $this->repository->findNextUndecoratedFixture();
    }

    public function getFixtureWithOddDecorationBySeason(SeasonStatistic $seasonStatistic): int
    {
        return $this->repository->getFixtureWithOddDecorationBySeason($seasonStatistic);
    }
}
