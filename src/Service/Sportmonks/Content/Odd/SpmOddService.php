<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Odd;

use App\Entity\SpmOdd;
use App\Service\Sportmonks\Content\Odd\Data\SpmOddData;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmOddService
{
    public function __construct(private SpmOddRepository $repository, private SpmOddFactory $factory, private EntityManagerInterface $entityManager)
    {
    }

    public function createByData(SpmOddData $data, bool $flush = true): SpmOdd
    {
        $spmOdd = $this->factory->createByData($data);
        $this->repository->save($spmOdd, $flush);
        return $spmOdd;
    }

    public function update(SpmOdd $spmOdd, SpmOddData $data): SpmOdd
    {
        $spmOdd = $this->factory->mapData($data, $spmOdd);
        $this->repository->save($spmOdd);
        return $spmOdd;
    }

    /**
     * @return SpmOdd[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $odds SpmOddData[]
     * @return int
     */
    public function createMultipleByData(array $odds): int
    {
        $stored = 0;
        foreach ($odds as $odd){
            if (!$this->repository->findBy(['apiId' => $odd->getApiId()])){
                $this->createByData($odd, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
