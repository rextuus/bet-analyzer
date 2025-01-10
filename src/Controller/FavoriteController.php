<?php

namespace App\Controller;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\CreateSimulatorFavoriteListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\RemoveSimulatorsType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\FavoriteListStatisticService;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationStatisticService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favorites')]
class FavoriteController extends AbstractController
{
    public function __construct(
        private SimulatorFavoriteListService $favoriteListService,
        private TipicoPlacementService $placementService,
        private SimulationStatisticService $simulationStatisticService,
        private SimulatorService $simulatorService,
        private FavoriteListStatisticService $favoriteListStatisticService,
    )
    {
    }

    #[Route('/list', name: 'app_favorite_list')]
    public function index(Request $request): Response
    {
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0);

        $fromQuery = $request->query->get('from');
        $untilQuery = $request->query->get('until');

        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+ 1day');
        $until->setTime(0, 0);

        if ($fromQuery) {
            $from->setTimestamp($fromQuery);
        }
        if ($untilQuery) {
            $until->setTimestamp($untilQuery);
        }

        $data = ['from' => $from];
        $form = $this->createFormBuilder($data)
            ->add('from', DateType::class, ['required' => false])
//            ->add('until', DateType::class, ['required' => false])
            ->add('filter', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $from = $data['from'];
            $from->setTime(0, 0);

            $until = clone $from; // Clone the $from DateTime object to avoid modifying it directly
            $until->modify('+1 day');
            $until->setTime(0, 0);
        }

        $favoriteLists = $this->favoriteListService->getAll();

        $balanceToday = [];
        $betsToday = [];
        $nextPlacements = [];
        $listClass = [];
        foreach ($favoriteLists as $favoriteList) {
            $simulators = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);
            $total = 0.0;
            $bets = 0;
            $possiblePlacements = 0;
            foreach ($simulators as $simulator) {
                $total = $total + $simulator['changeVolume'];
                $bets = $bets + $simulator['madeBets'];

                $simulator = $this->simulatorService->findBy(['id' => $simulator['id']])[0];
                $possiblePlacements = $possiblePlacements + count(
                        $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator)
                    );
                if ($from < $currentDate) {
                    $possiblePlacements = $bets;
                }
            }
            $balanceToday[] = $total;
            $betsToday[] = $bets;
            $nextPlacements[] = $possiblePlacements;

            $class = 'positive';
            if ($total < 0.0) {
                $class = 'negative';
            }
            $listClass[] = $class;
        }

        return $this->render('favorite/list.html.twig', [
            'form' => $form->createView(),
            'lists' => $favoriteLists,
            'balanceToday' => $balanceToday,
            'betsToday' => $betsToday,
            'listClass' => $listClass,
            'from' => $from->getTimestamp(),
            'until' => $until->getTimestamp(),
            'nextPlacements' => $nextPlacements,
        ]);
    }

    #[Route('/detail/{simulatorFavoriteList}', name: 'app_favorite_detail')]
    public function detail(
        Request $request,
        SimulatorFavoriteList $simulatorFavoriteList,
        FavoriteListStatisticService $favoriteListStatisticService
    ): Response
    {
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0);

        $fromQuery = $request->query->get('from');
        $untilQuery = $request->query->get('until');

        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+ 1day');
        $until->setTime(0, 0);

        if ($fromQuery) {
            $from->setTimestamp($fromQuery);
        }
        if ($untilQuery) {
            $until->setTimestamp($untilQuery);
        }

        $data = ['from' => $from];
        $form = $this->createFormBuilder($data)
            ->add('from', DateType::class, ['required' => false])
//            ->add('until', DateType::class, ['required' => false])
            ->add('filter', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $from = $data['from'];
            $from->setTime(0, 0);

            $until = clone $from; // Clone the $from DateTime object to avoid modifying it directly
            $until->modify('+1 day');
            $until->setTime(0, 0);
        }

        $container = $this->favoriteListStatisticService->getStatisticForFavoriteList(
            $simulatorFavoriteList,
            $from,
            $until
        );
        $placements = $this->placementService->findBySimulatorsAndDateTime($simulatorFavoriteList, $from, $until);

        $total = 0.0;
        $possiblePlacements = [];
        $simulatorClass = [];

        foreach ($placements as $placement) {
            $total = $total + $placement['changeVolume'];

            $class = 'positive';
            if ($placement['changeVolume'] < 0.0) {
                $class = 'negative';
            }
            $simulatorClass[] = $class;

            $madeBets = $placement['madeBets'];
            $placement = $this->simulatorService->findBy(['id' => $placement['id']])[0];
            if ($from > $currentDate) {
                $madeBets = count($this->simulationStatisticService->getUpcomingEventsForSimulator($placement));
            }
            $possiblePlacements[] = $madeBets;
        }

        $totalClass = 'positive';
        if ($total < 0.0) {
            $totalClass = 'negative';
        }

//        $chart = $favoriteListStatisticService->getFavoriteListStatisticForTimePeriod($simulatorFavoriteList);

        // history
        return $this->render('favorite/detail.html.twig', [
//            'form' => $form->createView(),
            'simulators' => $placements,
            'total' => $total,
            'name' => $simulatorFavoriteList->getIdentifier(),
            'totalClass' => $totalClass,
            'possiblePlacements' => $possiblePlacements,
            'simulatorClass' => $simulatorClass,
            'container' => $container,
            'favoriteList' => $simulatorFavoriteList,
//            'chart' => $chart,
        ]);
    }

    #[Route('/place/{simulatorFavoriteList}', name: 'app_favorite_place')]
    public function place(Request $request, SimulatorFavoriteList $simulatorFavoriteList): Response
    {
        $upcomingPlacements = [];
        $placementCount = 0;
        foreach ($simulatorFavoriteList->getSimulators() as $simulator) {
            $entry = [];
            $entry['simulator'] = $simulator;
            $entry['upcomingPlacements'] = $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator);
            $placementCount = $placementCount + count($entry['upcomingPlacements']);

            $params = json_decode($simulator->getStrategy()->getParameters(), true);
            $entry['targetBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
            $entry['searchBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
            $entry['activeOnCurrentWeekday'] = true;

            $entry['overUnderTarget'] = 0.0;
            if (array_key_exists(OverUnderStrategy::PARAMETER_TARGET_VALUE, $params)) {
                $entry['overUnderTarget'] = $params[OverUnderStrategy::PARAMETER_TARGET_VALUE];
            }

            if (array_key_exists(AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS, $params)) {
                $currentWeekday = (new DateTime())->format('N');
                $weekdays = $params[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS];

                $entry['activeOnCurrentWeekday'] = false;
                foreach ($weekdays as $weekday) {
                    if ($weekday == $currentWeekday) {
                        $entry['activeOnCurrentWeekday'] = true;
                    }
                }
            }

            $upcomingPlacements[] = $entry;
        }

        usort($upcomingPlacements, function ($a, $b) {
            return $b['activeOnCurrentWeekday'] <=> $a['activeOnCurrentWeekday'];
        });

        return $this->render(
            'favorite/place.html.twig',
            [
                'placementCount' => $placementCount,
                'invest' => $placementCount,
                'upcomingPlacements' => $upcomingPlacements,
                'list' => $simulatorFavoriteList
            ]
        );
    }

    #[Route('/place-time/{simulatorFavoriteList}', name: 'app_favorite_place_time_schedule')]
    public function placeScheduled(Request $request, SimulatorFavoriteList $simulatorFavoriteList): Response
    {
        $upcomingPlacements = [];
        $upcomingPlacementCount = 0;
        foreach ($simulatorFavoriteList->getSimulators() as $simulator) {
            foreach ($this->simulationStatisticService->getUpcomingEventsForSimulator($simulator) as $upcomingEvent) {
                $upcomingPlacementCount = $upcomingPlacementCount + 1;

                $entry = [];
                $entry['simulator'] = $simulator;
                $entry['upcomingPlacements'] = [$upcomingEvent];

                $params = json_decode($simulator->getStrategy()->getParameters(), true);
                $entry['targetBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
                $entry['searchBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
                $entry['activeOnCurrentWeekday'] = true;

                $entry['overUnderTarget'] = 0.0;
                if (array_key_exists(OverUnderStrategy::PARAMETER_TARGET_VALUE, $params)) {
                    $entry['overUnderTarget'] = $params[OverUnderStrategy::PARAMETER_TARGET_VALUE];
                }

                if (array_key_exists(AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS, $params)) {
                    $currentWeekday = (new DateTime())->format('N');
                    $weekdays = $params[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS];

                    $entry['activeOnCurrentWeekday'] = false;
                    foreach ($weekdays as $weekday) {
                        if ($weekday == $currentWeekday) {
                            $entry['activeOnCurrentWeekday'] = true;
                        }
                    }
                }

                $upcomingPlacements[] = $entry;
            }
        }

        usort($upcomingPlacements, function ($a, $b) {
            return $b['activeOnCurrentWeekday'] <=> $a['activeOnCurrentWeekday'];
        });

        usort($upcomingPlacements, function ($a, $b) {
            return $a['upcomingPlacements'][0]->getStartAtTimeStamp(
                ) <=> $b['upcomingPlacements'][0]->getStartAtTimeStamp();
        });

        return $this->render(
            'favorite/place.html.twig',
            [
                'placementCount' => $upcomingPlacementCount,
                'invest' => $upcomingPlacementCount,
                'upcomingPlacements' => $upcomingPlacements,
                'list' => $simulatorFavoriteList
            ]
        );
    }

    #[Route('/place-grouped/{simulatorFavoriteList}', name: 'app_favorite_place_grouped')]
    public function placeGrouped(Request $request, SimulatorFavoriteList $simulatorFavoriteList): Response
    {
        $upcomingPlacements = [];
        foreach ($simulatorFavoriteList->getSimulators() as $simulator) {
            $entry = [];
            $entry['simulator'] = $simulator;
            $entry['upcomingPlacements'] = $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator);

            $params = json_decode($simulator->getStrategy()->getParameters(), true);
            $entry['targetBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
            $entry['searchBetOn'] = BetOn::from($params[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);
            $entry['activeOnCurrentWeekday'] = true;

            if (array_key_exists(AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS, $params)) {
                $currentWeekday = (new DateTime())->format('N');
                $weekdays = $params[AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS];

                $entry['activeOnCurrentWeekday'] = false;
                foreach ($weekdays as $weekday) {
                    if ($weekday == $currentWeekday) {
                        $entry['activeOnCurrentWeekday'] = true;
                    }
                }
            }

            $upcomingPlacements[] = $entry;
        }

        usort($upcomingPlacements, function ($a, $b) {
            return $b['activeOnCurrentWeekday'] <=> $a['activeOnCurrentWeekday'];
        });

        // sort for same bet variants first
        $invest = 0;
        $combinedPlacements = [];
        foreach ($upcomingPlacements as $placementInfo) {
            foreach ($placementInfo['upcomingPlacements'] as $placement) {
                /** @var TipicoBet $placement */
                $key = $placement->getId() . '_' . $placementInfo['targetBetOn']->name;
                if (!array_key_exists($key, $combinedPlacements)) {
                    $combinedPlacements[$key] = [
                        'placementId' => $placement->getId(),
                        'variant' => $placementInfo['targetBetOn']->name,
                        'placements' => []
                    ];
                }
                $combinedPlacements[$key]['placements'][$placementInfo['simulator']->getIdentifier()] = $placement;
                $invest++;
            }
        }

        uasort($combinedPlacements, function ($a, $b) {
            return count($b) <=> count($a);
        });


        // sort for same fixtures second
        $groupedPlacements = [];
        foreach ($combinedPlacements as $key => $placementInfo) {
            $placementId = $placementInfo['placementId'];
            $variant = $placementInfo['variant'];
            if (!array_key_exists($placementId, $groupedPlacements)) {
                $groupedPlacements[$placementId] = [];
            }
            $groupedPlacements[$placementId][$variant] = $placementInfo['placements'];
        }

        uasort($groupedPlacements, function ($a, $b) {
            return count($b) <=> count($a);
        });


        uasort($groupedPlacements, function ($a, $b) {
            // Primary criterion: Total number of subchildren
            $totalA = array_sum(array_map('count', $a));
            $totalB = array_sum(array_map('count', $b));

            if ($totalA !== $totalB) {
                return $totalB <=> $totalA; // Descending order of total subchildren
            }

            // Secondary criterion: Maximum size of any subchild array
            $maxA = max(array_map('count', $a));
            $maxB = max(array_map('count', $b));

            return $maxB <=> $maxA; // Descending order of maximum subchild size
        });
        return $this->render(
            'favorite/place_grouped.html.twig',
            [
                'placementCount' => count($combinedPlacements),
                'invest' => $invest,
                'combinedPlacements' => $groupedPlacements,
                'list' => $simulatorFavoriteList
            ]
        );
    }

    #[Route('/create', name: 'app_favorite_create')]
    public function create(Request $request): Response
    {
        $simulatorFavoriteListData = new SimulatorFavoriteListData();
        $simulatorFavoriteListData->setCreated(new DateTime());

        $form = $this->createForm(CreateSimulatorFavoriteListType::class, $simulatorFavoriteListData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->favoriteListService->createByData($data);

            return $this->redirectToRoute('app_favorite_list', []);
        }

        return $this->render('favorite/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add/{simulator}', name: 'app_favorite_add')]
    public function add(Request $request, Simulator $simulator): Response
    {
        $simulatorFavoriteListData = new AddSimulatorToListData();
        $simulatorFavoriteListData->setSimulator($simulator);

        $form = $this->createForm(
            AddSimulatorToListType::class,
            $simulatorFavoriteListData,
            ['simulator' => $simulator,]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AddSimulatorToListData $data */
            $data = $form->getData();
            $favoriteList = $data->getSimulatorFavoriteList();
            $updateData = (new SimulatorFavoriteListData)->initFromEntity($favoriteList);
            $updateData->setSimulators([$data->getSimulator()]);
            $this->favoriteListService->update($favoriteList, $updateData);

            return $this->redirectToRoute(
                'app_tipico_simulation_detail',
                ['simulator' => $data->getSimulator()->getId()]
            );
        }

        return $this->render('favorite/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/remove/{simulatorFavoriteList}', name: 'app_favorite_remove')]
    public function remove(
        Request $request,
        SimulatorFavoriteList $simulatorFavoriteList,
        EntityManagerInterface $em
    ): Response {
        $simulators = $simulatorFavoriteList->getSimulators()->toArray();
        usort($simulators, function ($a, $b) {
            return $b->getCashBox() <=> $a->getCashBox(); // Descending order
        });


        $form = $this->createForm(RemoveSimulatorsType::class, null, [
            'simulators' => $simulators,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedSimulators = $form->get('simulators')->getData();

            foreach ($selectedSimulators as $simulator) {
                $simulatorFavoriteList->removeSimulator($simulator);
            }

            $em->flush();

            $this->addFlash('success', 'Selected simulators have been removed.');

            return $this->redirectToRoute(
                'app_favorite_detail',
                ['simulatorFavoriteList' => $simulatorFavoriteList->getId()]
            ); // Replace with your route name
        }

        return $this->render('favorite/remove.html.twig', [
            'form' => $form->createView(),
            'favoriteList' => $simulatorFavoriteList,
        ]);
    }

    #[Route('/test/{simulatorFavoriteList}', name: 'app_favorite_test')]
    public function test(
        SimulatorFavoriteList $simulatorFavoriteList,
    ): Response {
        return $this->render('favorite/test.html.twig', [
            'favoriteList' => $simulatorFavoriteList
        ]);
    }
}
