<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\TipicoBet;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBetService
{
    public function __construct(private readonly TipicoBetRepository $repository, private readonly TipicoBetFactory $factory)
    {
    }

    public function createByData(TipicoBetData $data): TipicoBet
    {
        $tipicoBet = $this->factory->createByData($data);
        $this->repository->save($tipicoBet);
        return $tipicoBet;
    }

    public function update(TipicoBet $tipicoBet, TipicoBetData $data): TipicoBet
    {
        $tipicoBet = $this->factory->mapData($data, $tipicoBet);
        $this->repository->save($tipicoBet);
        return $tipicoBet;
    }

    /**
     * @return TipicoBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByTipicoId(int $tipicoId): ?TipicoBet
    {
        return $this->repository->findOneBy(['tipicoId' => $tipicoId]);
    }

    /**
     * @return TipicoBet[]
     */
    public function findAllUndecoratedMatches(): array
    {
        return $this->repository->findAllUndecoratedMatches();
    }

    /**
     * @return TipicoBet[]
     */
    public function findInRange(float $min, float $max, string $targetOddColumn, array $alreadyUsed): array
    {
        return $this->repository->findInRange($min, $max, $targetOddColumn, $alreadyUsed);
    }
}