<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Placement\Data\LastWeekStatisticData;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Content\Placement\Data\TopSimulatorStatisticData;
use DateTime;

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

    public function getPlacementChangeComparedToDayBefore(Simulator $simulator, int $dayBefore): array
    {
        $daysBeforeStart = new DateTime('-'. $dayBefore .'day');
        $daysBeforeStart->setTime(0, 0);

        $end = '-'.($dayBefore - 1).' day';
        if ($dayBefore === 0) {
            $end = '+1 day';
        }
        $daysBeforeEnd = new DateTime($end);
        $daysBeforeEnd->setTime(0, 0);

        $placementChange = $this->repository->getPlacementChangeComparedToDayBefore($simulator, $daysBeforeStart, $daysBeforeEnd);
        $placementChange['weekday'] = $daysBeforeStart->format('l');
        $placementChange['formatted'] = $daysBeforeStart->format('d.m.y');

        return $placementChange;
    }

    public function getLastWeekStatistic(Simulator $simulator): LastWeekStatisticData
    {
        $statistic = new LastWeekStatisticData();

        for ($daysBefore = 7; $daysBefore >= 0; $daysBefore--) {
            $changeStatistic = $this->getPlacementChangeComparedToDayBefore($simulator, $daysBefore);
            foreach ($changeStatistic as $key => $value){
                $statistic->addValueByArrayKey($key, $value);
            }
        }
        $statistic->calculateRanks();

        return $statistic;
    }

    public function getTopSimulatorsOfLastDays(int $dayBefore, ?int $untilDays = null): TopSimulatorStatisticData
    {
        $from = new DateTime('-'. $dayBefore .'day');
        $from->setTime(0, 0);

        $until = new DateTime();
        $until->setTime(0, 0);
        if ($untilDays){
            $until = new DateTime('-'. $untilDays .'day');
            $until->setTime(0, 0);
        }

        $topSimulators = $this->repository->getTopSimulatorsOfLastDays($from, $until);

        return $this->createTopSimulatorStatisticData($from, $until, $topSimulators);
    }

    public function getTopSimulatorsOfCurrentDay(): TopSimulatorStatisticData
    {
        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+1 day');
        $until->setTime(0, 0);

        $topSimulators = $this->repository->getTopSimulatorsOfLastDays($from, $until);

        return $this->createTopSimulatorStatisticData($from, $until, $topSimulators);
    }

    public function createTopSimulatorStatisticData(DateTime $from, DateTime $until, array $topSimulators): TopSimulatorStatisticData
    {
        $statistic = new TopSimulatorStatisticData();
        $statistic->setFrom($from);
        $statistic->setUntil($until);
        foreach ($topSimulators as $simulator) {
            foreach ($simulator as $key => $value) {
                $statistic->addValueByArrayKey($key, $value);
            }
        }
        $statistic->calculateRanks();
        return $statistic;
    }

    public function findBySimulatorsAndDateTime(SimulatorFavoriteList $simulatorFavoriteList, DateTime $from, DateTime $until)
    {
        return $this->repository->findBySimulatorsAndDateTime($simulatorFavoriteList, $from, $until);
    }

    public function findTopSimulatorsByWeekday(array $ids)
    {
        return $this->repository->findTopSimulatorsByWeekday($ids);
    }

    /**
     * @return TipicoPlacement[]
     */
    public function getWeekdayStatisticForSimulator(Simulator $simulator): array
    {
        return $this->repository->getWeekdayStatisticForSimulator($simulator);
    }
}
