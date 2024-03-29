<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use App\Service\Tipico\Content\Placement\TipicoPlacementService;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\CreateSimulatorFavoriteListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use App\Service\Tipico\SimulationStatisticService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    )
    {
    }

    #[Route('/list', name: 'app_favorite_list')]
    public function index(Request $request): Response
    {
        $favoriteLists = $this->favoriteListService->getAll();

        $balanceToday = [];
        $betsToday = [];
        $nextPlacements = [];
        foreach ($favoriteLists as $favoriteList){
            $from = new DateTime();
            $from->setTime(0, 0);

            $until = new DateTime('+ 1day');
            $until->setTime(0, 0);

            $simulators = $this->placementService->findBySimulatorsAndDateTime($favoriteList, $from, $until);
            $total = 0.0;
            $bets = 0;
            $possiblePlacements = 0;
            foreach ($simulators as $simulator){
                $total = $total + $simulator['changeVolume'];
                $bets = $bets + $simulator['madeBets'];

                $simulator = $this->simulatorService->findBy(['id' => $simulator['id']])[0];
                $possiblePlacements = $possiblePlacements + count($this->simulationStatisticService->getUpcomingEventsForSimulator($simulator));
            }
            $balanceToday[] = $total;
            $betsToday[] = $bets;
            $nextPlacements[] = $possiblePlacements;

        }

        return $this->render('favorite/list.html.twig', [
            'lists' => $favoriteLists,
            'balanceToday' => $balanceToday,
            'betsToday' => $betsToday,
            'nextPlacements' => $nextPlacements,
        ]);
    }

    #[Route('/detail/{simulatorFavoriteList}', name: 'app_favorite_detail')]
    public function detail(SimulatorFavoriteList $simulatorFavoriteList): Response
    {
        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+ 1day');
        $until->setTime(0, 0);

        $simulators = $this->placementService->findBySimulatorsAndDateTime($simulatorFavoriteList, $from, $until);
        $total = 0.0;
        $possiblePlacements = [];
        foreach ($simulators as $simulator){
            $total = $total + $simulator['changeVolume'];

            $simulator = $this->simulatorService->findBy(['id' => $simulator['id']])[0];
            $possiblePlacements[] = count($this->simulationStatisticService->getUpcomingEventsForSimulator($simulator));
        }

        //dd($simulators);
        return $this->render('favorite/detail.html.twig', [
            'simulators' => $simulators,
            'total' => $total,
            'possiblePlacements' => $possiblePlacements,
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
