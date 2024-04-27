<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Score;

use App\Entity\Spm\SpmScore;
use App\Service\Sportmonks\Content\Score\Data\SpmScoreData;
use Doctrine\ORM\EntityManagerInterface;


class SpmScoreService
{
    public function __construct(private SpmScoreRepository $repository, private SpmScoreFactory $factory, private EntityManagerInterface $entityManager)
    {
    }

    public function createByData(SpmScoreData $data, bool $flush = true): SpmScore
    {
        $spmScore = $this->factory->createByData($data);
        $this->repository->save($spmScore, $flush);
        return $spmScore;
    }

    public function update(SpmScore $spmScore, SpmScoreData $data): SpmScore
    {
        $spmScore = $this->factory->mapData($data, $spmScore);
        $this->repository->save($spmScore);
        return $spmScore;
    }

    /**
     * @return SpmScore[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findScoresForFixture(int $fixtureApi): array
    {
        return $this->repository->findScoresForFixture($fixtureApi);
    }

    public function createMultipleByData(array $scores): int
    {
        $stored = 0;
        foreach ($scores as $score){
            if (!$this->repository->findBy(['apiId' => $score->getApiId()])){
                $this->createByData($score, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
