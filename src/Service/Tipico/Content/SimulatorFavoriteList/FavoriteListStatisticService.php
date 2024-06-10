<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationStatisticService;
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
        dump($placements);
        $total = 0.0;
        $possiblePlacements = 0;
        $actuallyMadePlacements = 0;
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

        dump($container);
        return $container;
    }
}
