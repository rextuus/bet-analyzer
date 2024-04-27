<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd;

use App\Entity\BettingProvider\TipicoHeadToHeadOdd;
use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\Data\TipicoHeadToHeadOddData;


class TipicoHeadToHeadOddService
{
    public function __construct(private readonly TipicoHeadToHeadOddRepository $repository, private readonly TipicoHeadToHeadOddFactory $factory)
    {
    }

    public function createByData(TipicoHeadToHeadOddData $data): TipicoHeadToHeadOdd
    {
        $tipicoHeadToHeadOdd = $this->factory->createByData($data);
        $this->repository->save($tipicoHeadToHeadOdd);
        return $tipicoHeadToHeadOdd;
    }

    public function update(TipicoHeadToHeadOdd $tipicoHeadToHeadOdd, TipicoHeadToHeadOddData $data): TipicoHeadToHeadOdd
    {
        $tipicoHeadToHeadOdd = $this->factory->mapData($data, $tipicoHeadToHeadOdd);
        $this->repository->save($tipicoHeadToHeadOdd);
        return $tipicoHeadToHeadOdd;
    }

    /**
     * @return TipicoHeadToHeadOdd[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByTipicoId(int $tipicoId): array
    {
        return $this->repository->findBy(['bet' => $tipicoId]);
    }
}
