<?php

namespace App\Service\Tipico\Message;

use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;
use App\Service\Tipico\Content\SimulatorDetailStatistic\SimulatorDetailStatisticService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Statistic\DetailStatisticService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateOrUpdateDetailStatisticMessageHandler
{


    public function __construct(
        private SimulatorService $simulatorService,
        private SimulatorDetailStatisticService $simulatorDetailStatisticService,
        private DetailStatisticService $detailStatisticService
    ) {
    }

    public function __invoke(CreateOrUpdateDetailStatisticMessage $message): void
    {
        $simulator = $this->simulatorService->find($message->getSimulatorId());
        $statistic = $simulator->getSimulatorDetailStatistic();

        $currentDetailStatistic = $this->detailStatisticService->generateDetailStatisticForSimulator($simulator);

        $data = new SimulatorDetailStatisticData();

        if ($statistic === null) {
            $data->setSimulator($simulator);
        } else {
            $data->initFromEntity($statistic);
        }

        foreach (Weekday::cases() as $weekday) {
            $placementDistribution = $currentDetailStatistic->getWeekDayPlacementDistributionByWeekday($weekday);
            if ($placementDistribution === null) {
                continue;
            }

            $total = $placementDistribution->getTotalSum();
            $placementsCount = count($placementDistribution->getPlacements());
            $average = $total / ($placementsCount ?: 1);

            match ($weekday) {
                Weekday::Monday => $data->setMondayTotal($total),
                Weekday::Tuesday => $data->setTuesdayTotal($total),
                Weekday::Wednesday => $data->setWednesdayTotal($total),
                Weekday::Thursday => $data->setThursdayTotal($total),
                Weekday::Friday => $data->setFridayTotal($total),
                Weekday::Saturday => $data->setSaturdayTotal($total),
                Weekday::Sunday => $data->setSundayTotal($total),
            };

            match ($weekday) {
                Weekday::Monday => $data->setMondayAverage($average),
                Weekday::Tuesday => $data->setTuesdayAverage($average),
                Weekday::Wednesday => $data->setWednesdayAverage($average),
                Weekday::Thursday => $data->setThursdayAverage($average),
                Weekday::Friday => $data->setFridayAverage($average),
                Weekday::Saturday => $data->setSaturdayAverage($average),
                Weekday::Sunday => $data->setSundayAverage($average),
            };
        }

        if ($statistic === null) {
            $this->simulatorDetailStatisticService->createByData($data);
        } else {
            $this->simulatorDetailStatisticService->update($statistic, $data);
        }
    }
}
