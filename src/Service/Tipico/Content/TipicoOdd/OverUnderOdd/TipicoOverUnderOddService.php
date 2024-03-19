<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\OverUnderOdd;

use App\Entity\TipicoOverUnderOdd;
use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\Data\TipicoOverUnderOddData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoOverUnderOddService
{
    public function __construct(private readonly TipicoOverUnderOddRepository $repository, private readonly TipicoOverUnderOddFactory $factory)
    {
    }

    public function createByData(TipicoOverUnderOddData $data): TipicoOverUnderOdd
    {
        $tipicoOdd = $this->factory->createByData($data);
        $this->repository->save($tipicoOdd);
        return $tipicoOdd;
    }

    public function update(TipicoOverUnderOdd $tipicoOdd, TipicoOverUnderOddData $data): TipicoOverUnderOdd
    {
        $tipicoOdd = $this->factory->mapData($data, $tipicoOdd);
        $this->repository->save($tipicoOdd);
        return $tipicoOdd;
    }

    /**
     * @return TipicoOverUnderOdd[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByTipicoId(int $getTipicoId)
    {
        return $this->repository->findBy(['bet' => $getTipicoId]);
    }
}
