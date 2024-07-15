<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoPlacement;
use App\Service\Tipico\Content\SimulatorDetailStatistic\Data\SimulatorDetailStatisticData;
use App\Service\Tipico\Content\SimulatorDetailStatistic\SimulatorDetailStatisticService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationChartService;
use App\Service\Tipico\Statistic\PlacementDistribution\WeekDayPlacementDistribution;
use DateTime;

class DetailStatisticService
{


    public function __construct(
        private SimulatorDetailStatisticService $simulatorDetailStatisticm,
        private SimulationChartService $chartService
    ) {
    }

    public function generateDetailStatisticForSimulator(Simulator $simulator): SimulatorDetailStatisticDto
    {
        $data = new SimulatorDetailStatisticData();
        if ($simulator->getSimulatorDetailStatistic()) {
            $data->initFromEntity($simulator->getSimulatorDetailStatistic());
        }

        $data->setSimulator($simulator);
        $data->setCreated(new DateTime());

        $orderedPlacements = StatisticHelper::getDailyPlacementDistribution(
            $simulator->getTipicoPlacements()->toArray()
        );

        $negativePeriods = [];
        $cashBox = 0.0;
        $lastHighestCashBox = 0.0;
        $lastHighestCashBoxDate = array_key_first($orderedPlacements);

        $currentCashBoxMinimum = 0.0;
        $currentCashBoxMinimumDate = array_key_first($orderedPlacements);

        /** @var WeekDayPlacementDistribution $dailyDistributions */
        $dailyDistributions = [];
        foreach ($orderedPlacements as $day => $placements) {
            // daily sum
            $dailySum = array_sum(
                array_map(
                    function (TipicoPlacement $placement) {
                        return $placement->getCalculatedValue();
                    },
                    $placements
                )
            );
            $cashBox = $cashBox + $dailySum;

            // current minimum cashBox value
            if ($cashBox < $currentCashBoxMinimum) {
                $currentCashBoxMinimum = $cashBox;
                $currentCashBoxMinimumDate = $day;
            }

            if ($cashBox > $lastHighestCashBox) {
                $startDate = DateTime::createFromFormat('d-m-Y', $lastHighestCashBoxDate);
                $endDate = DateTime::createFromFormat('d-m-Y', $day);

                $interval = $startDate->diff($endDate);

                $period = new NegativePeriod();
                $period->setDays($interval->days);
                $period->setStartDate($startDate);
                $period->setEndDate($endDate);
                $period->setStartAmount($lastHighestCashBox);
                $period->setEndAmount($cashBox);
                $period->setCashBoxMinimumDate(DateTime::createFromFormat('d-m-Y', $currentCashBoxMinimumDate));
                $period->setCashBoxMinimum($currentCashBoxMinimum);

                // adjust variables
                $lastHighestCashBox = $cashBox;
                $lastHighestCashBoxDate = $day;
                $currentCashBoxMinimum = $cashBox;
                $currentCashBoxMinimumDate = $day;

                $negativePeriods[] = $period;
            }

            // weekday sum
            $dayElement = DateTime::createFromFormat('d-m-Y', $day);
            $key = $dayElement->format('N');
            if (!array_key_exists($key, $dailyDistributions)) {
                $distribution = new WeekDayPlacementDistribution();
                $distribution->setWeekDay(Weekday::from((int)$key));
                $distribution->setPlacements([]);
                $dailyDistributions[$key] = $distribution;
            }
            $dailyDistributions[$key]->setPlacements(
                array_merge($dailyDistributions[$key]->getPlacements(), $placements)
            );
        }

        $longest = array_reduce($negativePeriods, function (?NegativePeriod $highest, ?NegativePeriod $current) {
            return ($highest === null || $current->getDays() > $highest->getDays()) ? $current : $highest;
        });

        $highestLost = array_reduce($negativePeriods, function (?NegativePeriod $lowest, ?NegativePeriod $current) {
            return ($lowest === null || $current->getCalculatedMinimumAmount() < $lowest->getCalculatedMinimumAmount(
                )) ? $current : $lowest;
        });

        $dto = new SimulatorDetailStatisticDto();
        $dto->setHighestLost($highestLost->getCashBoxMinimum());
        $dto->setLongestNonWinningPeriod($longest->getDays());


        usort(
            $dailyDistributions,
            function ($a, $b) {
                return $a->getWeekday()->value <=> $b->getWeekday()->value;
            }
        );

        foreach ($dailyDistributions as $dailyDistribution) {
            $dailyDistribution->setChart(
                $this->chartService->getBalanceColoredChart(
                    StatisticHelper::getDailyPlacementDistributionWithCalculatedCashBoxes(
                        $dailyDistribution->getPlacements()
                    )
                )
            );
        }

        $dto->setWeekDayPlacementDistributions($dailyDistributions);

        return $dto;
    }
}
