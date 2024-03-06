<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
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
        private readonly ChartBuilderInterface $chartBuilder
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

        $cashBoxValues = $this->getCashBoxChangeArray($placements->toArray());

        return $this->render('tipico_simulation/placements.html.twig', [
            'placements' => $placements,
            'cashBoxValues' => $cashBoxValues,
            'simulator' => $simulator,
        ]);
    }

    /**
     * @param TipicoPlacement[] $placements
     * @return float[]
     */
    private function getCashBoxChangeArray(array $placements): array
    {
        $betOutcomes = array_map(
            function (TipicoPlacement $placement) {
                $value = 0.0 - $placement->getInput();
                if ($placement->isWon()) {
                    $value = ($placement->getValue() * $placement->getInput()) - $placement->getInput();
                }
                return $value;
            },
            $placements
        );

        $cashBoxValues = [0 => 100.0];
        foreach ($betOutcomes as $nrr => $betOutcome) {
            $cashBoxValues[$nrr + 1] = $cashBoxValues[$nrr] + $betOutcome;
        }

        return $cashBoxValues;
    }
}
