<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Round;

use App\Entity\SpmRound;
use App\Service\Sportmonks\Content\Round\Data\SpmRoundData;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmRoundService
{
public function __construct(private SpmRoundRepository $repository, private SpmRoundFactory $factory)
{
}

public function createByData(SpmRoundData $data): SpmRound
{
    $spmRound = $this->factory->createByData($data);
    $this->repository->save($spmRound);
    return $spmRound;
}

public function update(SpmRound $spmRound, SpmRoundData $data): SpmRound
{
    $spmRound = $this->factory->mapData($data, $spmRound);
    $this->repository->save($spmRound);
    return $spmRound;
}

/**
 * @return SpmRound[]
 */
public function findBy(array $conditions): array
{
    return $this->repository->findBy($conditions);
}
}
