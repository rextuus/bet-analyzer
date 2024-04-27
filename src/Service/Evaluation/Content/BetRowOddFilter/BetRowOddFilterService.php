<?php

declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRowOddFilter;

use App\Entity\Spm\BetRowOddFilter;
use App\Service\Evaluation\Content\BetRowOddFilter\Data\BetRowOddFilterData;


class BetRowOddFilterService
{
    public function __construct(
        private readonly BetRowOddFilterRepository $repository,
        private readonly BetRowOddFilterFactory $factory
    ) {
    }

    public function createByData(BetRowOddFilterData $data, $flush = true): BetRowOddFilter
    {
        $betRowOddFilter = $this->factory->createByData($data);

        $this->repository->save($betRowOddFilter, $flush);
        return $betRowOddFilter;
    }

    public function update(BetRowOddFilter $betRowOddFilter, BetRowOddFilterData $data): BetRowOddFilter
    {
        $betRowOddFilter = $this->factory->mapData($data, $betRowOddFilter);
        $this->repository->save($betRowOddFilter);
        return $betRowOddFilter;
    }

    /**
     * @return BetRowOddFilter[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findById(int $id): ?BetRowOddFilter
    {
        return $this->repository->findOneBy(['id' => $id]);
    }
}
