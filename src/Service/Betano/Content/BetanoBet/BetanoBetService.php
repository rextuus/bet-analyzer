<?php
declare(strict_types=1);

namespace App\Service\Betano\Content\BetanoBet;

use App\Entity\BetanoBet;
use App\Service\Betano\Content\BetanoBet\Data\BetanoBetData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetanoBetService
{
    public function __construct(private readonly BetanoBetRepository $repository, private readonly BetanoBetFactory $factory)
    {
    }

    public function createByData(BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->createByData($data);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    public function update(BetanoBet $betanoBet, BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->mapData($data, $betanoBet);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    /**
     * @return BetanoBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }
}
