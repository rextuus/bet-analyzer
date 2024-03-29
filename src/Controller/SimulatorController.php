<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\CreateSimulatorFavoriteListType;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/simulator')]
class SimulatorController extends AbstractController
{


    public function __construct(private SimulatorFavoriteListService $favoriteListService)
    {
    }

    #[Route('/list', name: 'app_simulator_list')]
    public function index(Request $request): Response
    {
        $favoriteLists = $this->favoriteListService->getAll();
        return $this->render('simulator/list.html.twig', [
            'lists' => $favoriteLists,
        ]);
    }

    #[Route('/create', name: 'app_simulator_create')]
    public function create(Request $request): Response
    {
        $simulatorFavoriteListData = new SimulatorFavoriteListData();
        $simulatorFavoriteListData->setCreated(new DateTime());

        $form = $this->createForm(CreateSimulatorFavoriteListType::class, $simulatorFavoriteListData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->favoriteListService->createByData($data);

            return $this->redirectToRoute('app_simulator_list', []);
        }

        return $this->render('simulator/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add/{simulator}', name: 'app_simulator_add')]
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

        return $this->render('simulator/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
