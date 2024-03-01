<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement;

use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoPlacementService
{
    public function __construct(private readonly TipicoPlacementRepository $repository, private readonly TipicoPlacementFactory $factory)
    {
    }

    public function createByData(TipicoPlacementData $data): TipicoPlacement
    {
        $tipicoPlacement = $this->factory->createByData($data);
        $this->repository->save($tipicoPlacement);
        return $tipicoPlacement;
    }

    public function update(TipicoPlacement $tipicoPlacement, TipicoPlacementData $data): TipicoPlacement
    {
        $tipicoPlacement = $this->factory->mapData($data, $tipicoPlacement);
        $this->repository->save($tipicoPlacement);
        return $tipicoPlacement;
    }

    /**
     * @return TipicoPlacement[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }
}
