<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRow\SimpleBetRow;

use App\Entity\BetRowOddFilter;
use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data\SimpleBetRowData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SimpleBetRowService
{
    public function __construct(private readonly SimpleBetRowRepository $repository, private readonly SimpleBetRowFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
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

    /**
     * @param $simpleBetRows SimpleBetRowData[]
     * @return int
     */
    public function createMultipleByData(array $simpleBetRows): int
    {
        $stored = 0;
        foreach ($simpleBetRows as $simpleBetRow) {
            if (!$this->repository->findBy(['apiId' => $simpleBetRow->getApiId()])) {
                $this->createByData($simpleBetRow, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }

    public function findBySeasonAndFilter(SpmSeason $season, BetRowOddFilter $filter): int
    {
        return $this->repository->findBySeasonAndFilter($season, $filter);
    }

    public function findBySeasonIncludingSummaries(SpmSeason $season)
    {
        return $this->repository->findBySeasonIncludingSummaries($season);
    }
}
