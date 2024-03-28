<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class SimulatorFavoriteListService
{
    public function __construct(private readonly SimulatorFavoriteListRepository $repository, private readonly SimulatorFavoriteListFactory $factory)
    {
    }

    public function createByData(SimulatorFavoriteListData $data): SimulatorFavoriteList
    {
        $simulatorFavoriteList = $this->factory->createByData($data);
        $this->repository->save($simulatorFavoriteList);
        return $simulatorFavoriteList;
    }

    public function update(SimulatorFavoriteList $simulatorFavoriteList, SimulatorFavoriteListData $data): SimulatorFavoriteList
    {
        $simulatorFavoriteList = $this->factory->mapData($data, $simulatorFavoriteList);
        $this->repository->save($simulatorFavoriteList);
        return $simulatorFavoriteList;
    }

    /**
     * @return SimulatorFavoriteList[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @return SimulatorFavoriteList[]
     */
    public function findListsNotContainingSimulator(Simulator $simulator): array
    {
        return $this->repository->findListsNotContainingSimulator($simulator);
    }

    /**
     * @return SimulatorFavoriteList[]
     */
    public function findAllWithPlacements(): array
    {
        return $this->repository->findAllWithPlacements();
    }


    /**
     * @return SimulatorFavoriteList[]
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }
}
