<?php

namespace App\Controller;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\CreateSimulatorFavoriteListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\FavoriteListStatisticService;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use App\Service\Tipico\SimulationStatisticService;
use DateTime;
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

        if ($fromQuery){
            $from->setTimestamp($fromQuery);
        }
        if ($untilQuery){
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
        foreach ($favoriteLists as $favoriteList){
            $simulators = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);
            $total = 0.0;
            $bets = 0;
            $possiblePlacements = 0;
            foreach ($simulators as $simulator){
                $total = $total + $simulator['changeVolume'];
                $bets = $bets + $simulator['madeBets'];

                $simulator = $this->simulatorService->findBy(['id' => $simulator['id']])[0];
                $possiblePlacements = $possiblePlacements + count($this->simulationStatisticService->getUpcomingEventsForSimulator($simulator));
                if ($from < $currentDate){
                    $possiblePlacements = $bets;
                }
            }
            $balanceToday[] = $total;
            $betsToday[] = $bets;
            $nextPlacements[] = $possiblePlacements;

            $class = 'positive';
            if ($total < 0.0){
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
    public function detail(Request $request, SimulatorFavoriteList $simulatorFavoriteList): Response
    {
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0);

        $fromQuery = $request->query->get('from');
        $untilQuery = $request->query->get('until');

        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+ 1day');
        $until->setTime(0, 0);

        if ($fromQuery){
            $from->setTimestamp($fromQuery);
        }
        if ($untilQuery){
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
            if ($from > $currentDate){
                $madeBets = count($this->simulationStatisticService->getUpcomingEventsForSimulator($placement));
            }
            $possiblePlacements[] = $madeBets;

        }

        $totalClass = 'positive';
        if ($total < 0.0){
            $totalClass = 'negative' ;
        }

        // history
        return $this->render('favorite/detail.html.twig', [
            'form' => $form->createView(),
            'simulators' => $placements,
            'total' => $total,
            'name' => $simulatorFavoriteList->getIdentifier(),
            'totalClass' => $totalClass,
            'possiblePlacements' => $possiblePlacements,
            'simulatorClass' => $simulatorClass,
            'container' => $container,
        ]);
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

        $form = $this->createForm(AddSimulatorToListType::class, $simulatorFavoriteListData, ['simulator' => $simulator,]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AddSimulatorToListData $data */
            $data = $form->getData();
            $favoriteList = $data->getSimulatorFavoriteList();
            $updateData = (new SimulatorFavoriteListData)->initFromEntity($favoriteList);
            $updateData->setSimulators([$data->getSimulator()]);
            $this->favoriteListService->update($favoriteList, $updateData);

            return $this->redirectToRoute('app_tipico_simulation_detail', ['simulator' => $data->getSimulator()->getId()]);
        }

        return $this->render('favorite/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
