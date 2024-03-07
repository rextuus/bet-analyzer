<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use App\Service\Tipico\SimulationStatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/tipico/simulation')]
class TipicoSimulationController extends AbstractController
{
    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStatisticService $simulationStatisticService
    )
    {
    }

    #[Route('/', name: 'app_tipico_simulation_dashboard')]
    public function index(Request $request, SimulationStrategyProcessorProvider $processorProvider): Response
    {
        $idents = $processorProvider->getIdents();
        $choices = [];
        foreach ($idents as $ident){
            $choices[$ident] = $ident;
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('excludeNegative', CheckboxType::class, ['attr' => ['checked' => 1], 'required' => false])
            ->add('variant', ChoiceType::class, ['multiple' => true, 'choices' => $choices])
            ->add('filter', SubmitType::class)
            ->getForm();

        $data = ['excludeNegative' => true];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
        }
        $simulators = $this->simulatorService->findByFilter($data);

        return $this->render('tipico_simulation/dashboard.html.twig', [
            'simulators' => $simulators,
            'filter' => $form,
        ]);
    }

    #[Route('/{simulator}/placements', name: 'app_tipico_simulation_placements')]
    public function placements(Simulator $simulator): Response
    {
        $placements = $simulator->getTipicoPlacements();

        $cashBoxValues = $this->simulationStatisticService->getCashBoxChangeArray($placements->toArray());

        return $this->render('tipico_simulation/placements.html.twig', [
            'placements' => $placements,
            'cashBoxValues' => $cashBoxValues,
            'simulator' => $simulator,
        ]);
    }

    #[Route('/{simulator}/detail', name: 'app_tipico_simulation_detail')]
    public function detail(Simulator $simulator): Response
    {
        $chart = $this->simulationStatisticService->getCashBoxChart($simulator);

        return $this->render('tipico_simulation/detail.html.twig', [
            'chart' => $chart,
        ]);
    }
}
