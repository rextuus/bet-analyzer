<?php

declare(strict_types=1);

namespace App\Service\Statistic\Content\OddOutcome;

use App\Entity\Spm\OddOutcome;
use App\Service\Evaluation\BetOn;
use App\Service\Statistic\Content\OddOutcome\Data\OddOutcomeData;


class OddOutcomeService
{
    public function __construct(
        private readonly OddOutcomeRepository $repository,
        private readonly OddOutcomeFactory $factory
    ) {
    }

    public function createByData(OddOutcomeData $data, $flush = true): OddOutcome
    {
        $oddOutcome = $this->factory->createByData($data);
        $this->repository->save($oddOutcome, $flush);
        return $oddOutcome;
    }

    public function update(OddOutcome $oddOutcome, OddOutcomeData $data): OddOutcome
    {
        $oddOutcome = $this->factory->mapData($data, $oddOutcome);
        $this->repository->save($oddOutcome);
        return $oddOutcome;
    }

    /**
     * @return OddOutcome[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByRangeAndVariant(float $min, float $max, BetOn $betOn): ?OddOutcome
    {
        $results = $this->findBy(['min' => $min, 'max' => $max, 'betOn' => $betOn->value]);
        if (count($results) === 1) {
            return $results[0];
        }

        return null;
    }
}
