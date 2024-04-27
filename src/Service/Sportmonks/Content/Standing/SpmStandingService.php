<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Standing;

use App\Entity\Spm\SpmStanding;
use App\Service\Sportmonks\Content\Standing\Data\SpmStandingData;
use Doctrine\ORM\EntityManagerInterface;


class SpmStandingService
{
    public function __construct(private readonly SpmStandingRepository $repository, private readonly SpmStandingFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(SpmStandingData $data, $flush = true): SpmStanding
    {
        $spmStanding = $this->factory->createByData($data);
        $this->repository->save($spmStanding, $flush);
        return $spmStanding;
    }

    public function update(SpmStanding $spmStanding, SpmStandingData $data): SpmStanding
    {
        $spmStanding = $this->factory->mapData($data, $spmStanding);
        $this->repository->save($spmStanding);
        return $spmStanding;
    }

    /**
     * @return SpmStanding[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $spmStandings SpmStandingData[]
     * @return int
     */
    public function createMultipleByData(array $spmStandings): int
    {
        $stored = 0;
        foreach ($spmStandings as $spmStanding){
            if (!$this->repository->findBy(['apiId' => $spmStanding->getApiId()])){
                $this->createByData($spmStanding, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
