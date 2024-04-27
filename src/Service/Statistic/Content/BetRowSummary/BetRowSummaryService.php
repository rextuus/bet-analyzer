<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowSummary;

use App\Entity\Spm\BetRowSummary;
use App\Service\Statistic\Content\BetRowSummary\Data\BetRowSummaryData;
use Doctrine\ORM\EntityManagerInterface;


class BetRowSummaryService
{
    public function __construct(private readonly BetRowSummaryRepository $repository, private readonly BetRowSummaryFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(BetRowSummaryData $data, $flush = true): BetRowSummary
    {
        $betRowSummary = $this->factory->createByData($data);
        $this->repository->save($betRowSummary, $flush);
        return $betRowSummary;
    }

    public function update(BetRowSummary $betRowSummary, BetRowSummaryData $data): BetRowSummary
    {
        $betRowSummary = $this->factory->mapData($data, $betRowSummary);
        $this->repository->save($betRowSummary);
        return $betRowSummary;
    }

    /**
     * @return BetRowSummary[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $betRowSummarys BetRowSummaryData[]
     * @return int
     */
    public function createMultipleByData(array $betRowSummarys): int
    {
        $stored = 0;
        foreach ($betRowSummarys as $betRowSummary) {
            if (!$this->repository->findBy(['betRow' => $betRowSummary->getBetRow()])) {
                $this->createByData($betRowSummary, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
