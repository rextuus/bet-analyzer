<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\SeasonSummary;

use App\Entity\SeasonSummary;
use App\Service\Statistic\Content\SeasonSummary\Data\SeasonSummaryData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SeasonSummaryService
{
    public function __construct(private readonly SeasonSummaryRepository $repository, private readonly SeasonSummaryFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
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

    /**
     * @param $seasonSummarys SeasonSummaryData[]
     * @return int
     */
    public function createMultipleByData(array $seasonSummarys): int
    {
        $stored = 0;
        foreach ($seasonSummarys as $seasonSummary) {
            if (!$this->repository->findBy(['id' => $seasonSummary->getId()])) {
                $this->createByData($seasonSummary, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
