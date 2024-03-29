<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\SimulatorFavoriteList;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationStatisticService;
use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class FavoriteListStatisticService
{
    public function __construct(
        private SimulatorFavoriteListService $favoriteListService,
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
        $container = new FavoriteListStatisticContainer();

//        $from = new DateTime();
//        $from->setTime(0, 0);
//
//        $until = new DateTime('+ 1day');
//        $until->setTime(0, 0);

        $simulators = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);
        $totalBalance = 0.0;
        $doneBets = 0;
        $possiblePlacements = 0;
        foreach ($simulators as $simulator) {
            $totalBalance = $totalBalance + $simulator['changeVolume'];
            $doneBets = $doneBets + $simulator['madeBets'];

            $simulator = $this->simulatorService->findBy(['id' => $simulator['id']])[0];
            $possiblePlacements = $possiblePlacements + count($this->simulationStatisticService->getUpcomingEventsForSimulator($simulator));
        }

        $container->setPossiblePlacements($possiblePlacements);
        $container->setCurrentBalance($totalBalance);
        $container->setDonePlacements($doneBets);

        return $container;
    }
}
