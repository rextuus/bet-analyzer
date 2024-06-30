<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationStatisticService;
use DateInterval;
use DatePeriod;
use DateTime;


class FavoriteListStatisticService
{
    public function __construct(
        private TipicoPlacementService $placementService,
        private SimulationStatisticService $simulationStatisticService,
        private SimulatorService $simulatorService,
    )
    {
    }

    public function getStatisticForFavoriteList(
        SimulatorFavoriteList $favoriteList,
        DateTime $from,
        DateTime $until
    ): FavoriteListStatisticContainer
    {
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0);

        $container = new FavoriteListStatisticContainer();
        $placements = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);

        $total = 0.0;
        $possiblePlacements = 0;
        $actuallyMadePlacements = 0;
        $madeBets = 0;
        $simulatorContainer = [];
        foreach ($favoriteList->getSimulators() as $simulator) {
            $hasBets = false;
            foreach ($placements as $placement) {
                if ($placement['id'] !== $simulator->getId()) {
                    continue;
                }
                $hasBets = true;

                $currentChangeVolume = $placement['changeVolume'];
                $total = $total + $currentChangeVolume;

                $class = 'positive';
                if ($placement['changeVolume'] < 0.0) {
                    $class = 'negative';
                }

                $madeBets = $placement['madeBets'];
                $possibleBets = $placement['madeBets'];
                $placement = $this->simulatorService->findBy(['id' => $placement['id']])[0];

                // possible and made should only differ on current day. otherwise something is broken => TODO find better way
                if ($from >= $currentDate) {
                    $possibleBets = count($this->simulationStatisticService->getUpcomingEventsForSimulator($placement));
                }


                $possiblePlacements = $possiblePlacements + $possibleBets;
                $actuallyMadePlacements = $actuallyMadePlacements + $madeBets;

                $simulatorData = new FavoriteListStatisticSimulatorContainer();
                $simulatorData->setSimulator($simulator);
                $simulatorData->setSimulatorClass($class);
                $simulatorData->setDonePlacements($madeBets);
                $simulatorData->setPossiblePlacements($possibleBets);
                $simulatorData->setCurrentBalance($currentChangeVolume);
                $simulatorContainer[] = $simulatorData;
            }

            if (!$hasBets) {
                $simulatorData = new FavoriteListStatisticSimulatorContainer();
                $simulatorData->setSimulator($simulator);
                $simulatorData->setSimulatorClass('negative');
                $simulatorData->setDonePlacements(0);
                $simulatorData->setPossiblePlacements(0);
                $simulatorData->setCurrentBalance(0.0);
                $simulatorContainer[] = $simulatorData;
            }
        }


        $totalClass = 'positive';
        if ($total < 0.0) {
            $totalClass = 'negative';
        }
        $container->setSimulatorDetails($simulatorContainer);

        $container->setPossiblePlacements($possiblePlacements);
        $container->setCurrentBalance($total);
        $container->setDonePlacements($madeBets);
        $container->setListClass($totalClass);

        return $container;
    }

    public function getWeekdayStatisticForFavoriteList(
        SimulatorFavoriteList $favoriteList,
        DateTime $from,
        DateTime $until,
        Weekday $weekday
    ): FavoriteListStatisticContainer {
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0);

        $container = new FavoriteListStatisticContainer();

        $days = $this->getDateOfSpecificWeekdayLastYear($weekday);
        foreach ($days as $day) {
            $placements = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);

            $total = 0.0;
            $possiblePlacements = 0;
            $actuallyMadePlacements = 0;
            $madeBets = 0;
            $simulatorContainer = [];
            foreach ($favoriteList->getSimulators() as $simulator) {
                $hasBets = false;
                foreach ($placements as $placement) {
                    if ($placement['id'] !== $simulator->getId()) {
                        continue;
                    }
                    $hasBets = true;

                    $currentChangeVolume = $placement['changeVolume'];
                    $total = $total + $currentChangeVolume;

                    $class = 'positive';
                    if ($placement['changeVolume'] < 0.0) {
                        $class = 'negative';
                    }

                    $madeBets = $placement['madeBets'];
                    $possibleBets = $placement['madeBets'];
                    $placement = $this->simulatorService->findBy(['id' => $placement['id']])[0];

                    // possible and made should only differ on current day. otherwise something is broken => TODO find better way
                    if ($from > $currentDate) {
                        $possibleBets = count(
                            $this->simulationStatisticService->getUpcomingEventsForSimulator($placement)
                        );
                    }

                    $possiblePlacements = $possiblePlacements + $possibleBets;
                    $actuallyMadePlacements = $actuallyMadePlacements + $madeBets;

                    $simulatorData = new FavoriteListStatisticSimulatorContainer();
                    $simulatorData->setSimulator($simulator);
                    $simulatorData->setSimulatorClass($class);
                    $simulatorData->setDonePlacements($madeBets);
                    $simulatorData->setPossiblePlacements($possibleBets);
                    $simulatorData->setCurrentBalance($currentChangeVolume);
                    $simulatorContainer[] = $simulatorData;
                }

                if (!$hasBets) {
                    $simulatorData = new FavoriteListStatisticSimulatorContainer();
                    $simulatorData->setSimulator($simulator);
                    $simulatorData->setSimulatorClass('negative');
                    $simulatorData->setDonePlacements(0);
                    $simulatorData->setPossiblePlacements(0);
                    $simulatorData->setCurrentBalance(0.0);
                    $simulatorContainer[] = $simulatorData;
                }
            }


            $totalClass = 'positive';
            if ($total < 0.0) {
                $totalClass = 'negative';
            }
            $container->setSimulatorDetails($simulatorContainer);

            $container->setPossiblePlacements($possiblePlacements);
            $container->setCurrentBalance($total);
            $container->setDonePlacements($madeBets);
            $container->setListClass($totalClass);
        }


        return $container;
    }

    public function getDateOfSpecificWeekdayLastYear(Weekday $weekday): array
    {
        $startDate = new DateTime();
        $startDate->setDate((int)$startDate->format('Y'), 1, 1); // set date to January 1st of this year
        $startDate->setTime(0, 0);
        $endDate = new DateTime();
        $endDate->setDate((int)$endDate->format('Y'), 12, 31); // set date to December 31st of this year
        $endDate->setTime(23, 59, 59);
        $endDate = new DateTime();

        // Define the date period (interval of 1 day)
        $dateInterval = new DateInterval("P1D");
        $dateRange = new DatePeriod($startDate, $dateInterval, $endDate);

        $dates = [];

        // Iterate over the date range
        foreach ($dateRange as $date) {
            // Check if the day of the week of the current date equals the required day of the week
            if ($date->format('N') === (string)$weekday->value) {
                $dates[] = $date;
            }
        }

        return $dates;
    }
}
