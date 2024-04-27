<?php

declare(strict_types=1);

namespace App\Service\Statistic\Content\SeasonSummary;

use App\Entity\Spm\SeasonSummary;
use App\Service\Statistic\Content\SeasonSummary\Data\SeasonSummaryData;


class SeasonSummaryService
{
    public function __construct(
        private readonly SeasonSummaryRepository $repository,
        private readonly SeasonSummaryFactory $factory
    ) {
    }

    public function createByData(SeasonSummaryData $data, $flush = true): SeasonSummary
    {
        $seasonSummary = $this->factory->createByData($data);
        $this->repository->save($seasonSummary, $flush);
        return $seasonSummary;
    }

    public function update(SeasonSummary $seasonSummary, SeasonSummaryData $data): SeasonSummary
    {
        $seasonSummary = $this->factory->mapData($data, $seasonSummary);
        $this->repository->save($seasonSummary);
        return $seasonSummary;
    }

    /**
     * @return SeasonSummary[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }
}
