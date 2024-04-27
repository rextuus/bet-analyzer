<?php

declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRow\SimpleBetRow;

use App\Entity\Spm\BetRowOddFilter;
use App\Entity\Spm\SimpleBetRow;
use App\Entity\Spm\SpmSeason;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data\SimpleBetRowData;


class SimpleBetRowService
{
    public function __construct(
        private readonly SimpleBetRowRepository $repository,
        private readonly SimpleBetRowFactory $factory
    ) {
    }

    public function createByData(SimpleBetRowData $data, $flush = true): SimpleBetRow
    {
        $simpleBetRow = $this->factory->createByData($data);
        $this->repository->save($simpleBetRow, $flush);
        return $simpleBetRow;
    }

    public function update(SimpleBetRow $simpleBetRow, SimpleBetRowData $data): SimpleBetRow
    {
        $simpleBetRow = $this->factory->mapData($data, $simpleBetRow);
        $this->repository->save($simpleBetRow);
        return $simpleBetRow;
    }

    /**
     * @return SimpleBetRow[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findById(int $id): ?SimpleBetRow
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function findBySeasonAndFilter(SpmSeason $season, BetRowOddFilter $filter): int
    {
        return $this->repository->findBySeasonAndFilter($season, $filter);
    }

    public function findBySeasonIncludingSummaries(SpmSeason $season)
    {
        return $this->repository->findBySeasonIncludingSummaries($season);
    }

    /**
     * @param BetRowOddFilter[] $filter
     */
    public function findRowsWithFilter(array $filter)
    {
        return $this->repository->findRowsWithFilter($filter);
    }
}
