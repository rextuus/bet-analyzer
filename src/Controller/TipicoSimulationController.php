<?php

namespace App\Controller;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterType;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use App\Service\Tipico\SimulationStatisticService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipico/simulation')]
class TipicoSimulationController extends AbstractController
{
    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStatisticService $simulationStatisticService,
    )
    {
    }

    #[Route('/', name: 'app_tipico_simulation_dashboard')]
    public function index(): Response
    {
        $nextPlacements = $this->simulationStatisticService->getDailyEvents();
        $chartHome = $this->simulationStatisticService->getDailyEventChart();

        $current = new DateTime();
        $total = count($nextPlacements);

        $open = array_filter(
            $nextPlacements,
            function (TipicoBet $tipicoBet) use ($current) {
                return $tipicoBet->getStartAtTimeStamp()/1000 > $current->getTimestamp();
            }
        );
        $open = count($open);

        $lastWeekStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(7);
        $beforeLastWeekStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(14, 7);
        $lastSameWeekDayStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(7, 6);
        $yesterdayStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(1);
        $currentDayStatistic = $this->simulationStatisticService->getTopSimulatorsOfCurrentDay();
        $cashBoxChart = $this->simulationStatisticService->getSimulatorCashBoxDistributionChart();
        $distribution = $this->simulationStatisticService->getActiveSimulators();


        $from = new DateTime();
        $from->setTime(0, 0);

        $until = new DateTime('+ 1day');
        $until->setTime(0, 0);

        return $this->render('tipico_simulation/dashboard.html.twig', [
            'nextPlacements' => $nextPlacements,
            'betOn' => BetOn::HOME,
            'total' => $total,
            'open' => $open,
            'finished' => $total - $open,
            'chartHome' => $chartHome,
            'lastWeekStatistic' => $lastWeekStatistic,
            'beforeLastWeekStatistic' => $beforeLastWeekStatistic,
            'lastSameWeekDayStatistic' => $lastSameWeekDayStatistic,
            'yesterdayStatistic' => $yesterdayStatistic,
            'currentDayStatistic' => $currentDayStatistic,
            'cashBoxChart' => $cashBoxChart,
            'totalSimulators' => $distribution['total'],
            'inactiveSimulators' => $distribution['inactive'],
            'activeSimulators' => $distribution['active'],
            'inWinSimulators' => $distribution['inWin'],
            'from' => $from->getTimestamp(),
            'until' => $until->getTimestamp(),
        ]);
    }

    #[Route('/list', name: 'app_tipico_simulator_list')]
    public function list(Request $request, SimulationStrategyProcessorProvider $processorProvider): Response
    {
        $idents = $processorProvider->getIdents();
        $choices = [];
        foreach ($idents as $ident) {
            $choices[$ident] = $ident;
        }

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('excludeNegative', CheckboxType::class, ['attr' => ['checked' => 1], 'required' => false])
            ->add('variant', ChoiceType::class, ['multiple' => true, 'choices' => $choices])
            ->add('filter', SubmitType::class)
            ->getForm();

        $filter = new SimulatorFilterData();
        $form = $this->createForm(SimulatorFilterType::class, $filter);

        $data = ['excludeNegative' => true];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filter = $form->getData();
        }
        $simulators = $this->simulatorService->findByFilter($filter);

        return $this->render('tipico_simulation/list.html.twig', [
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
        $cashBoxChart = $this->simulationStatisticService->getCashBoxChart($simulator);
        $dailyDistributionChart = $this->simulationStatisticService->getDailyDistributionChart($simulator);
        $valueToWinDistributionChart = $this->simulationStatisticService->getValueToWinDistributionChart($simulator);

        $strategy = $simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);

        $targetBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
        $searchBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);

        $overUnderTarget = null;
        if($strategy->getIdentifier() === OverUnderStrategy::IDENT){
            $overUnderTarget = $parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE];
        }

        $nextPlacements = $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator, 200);

        $statistics = $this->simulationStatisticService->getStatistics($simulator);

        $nonPlacedBets = $this->simulationStatisticService->getNonPlacedBets($simulator);

        return $this->render('tipico_simulation/detail.html.twig', [
            'simulator' => $simulator,
            'statistics' => $statistics,
            'cashBoxChart' => $cashBoxChart,
            'dailyDistributionChart' => $dailyDistributionChart,
            'valueToWinDistributionChart' => $valueToWinDistributionChart,
            'nextPlacements' => $nextPlacements,
            'targetBetOn' => $targetBetOn,
            'searchBetOn' => $searchBetOn,
            'nonPlacedBets' => count($nonPlacedBets),
            'overUnderTarget' => $overUnderTarget,
            'lastWeekStatistic' => $this->simulationStatisticService->getPlacementChangeComparedToDayBefore($simulator),
        ]);
    }

    #[Route('/{simulator}/instruction', name: 'app_tipico_simulation_instruction')]
    public function instruction(Simulator $simulator): Response
    {
        $cashBoxChart = $this->simulationStatisticService->getCashBoxChart($simulator);
        $dailyDistributionChart = $this->simulationStatisticService->getDailyDistributionChart($simulator);
        $valueToWinDistributionChart = $this->simulationStatisticService->getValueToWinDistributionChart($simulator);

        $strategy = $simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);

        $targetBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);
        $searchBetOn = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_SEARCH_BET_ON]);

        $overUnderTarget = null;
        if($strategy->getIdentifier() === OverUnderStrategy::IDENT){
            $overUnderTarget = $parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE];
        }

        $nextPlacements = $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator, 200);

        $statistics = $this->simulationStatisticService->getStatistics($simulator);

        $nonPlacedBets = $this->simulationStatisticService->getNonPlacedBets($simulator);

        return $this->render('tipico_simulation/instruction.html.twig', [
            'simulator' => $simulator,
            'statistics' => $statistics,
            'cashBoxChart' => $cashBoxChart,
            'dailyDistributionChart' => $dailyDistributionChart,
            'valueToWinDistributionChart' => $valueToWinDistributionChart,
            'nextPlacements' => $nextPlacements,
            'targetBetOn' => $targetBetOn,
            'searchBetOn' => $searchBetOn,
            'nonPlacedBets' => count($nonPlacedBets),
            'overUnderTarget' => $overUnderTarget,
            'lastWeekStatistic' => $this->simulationStatisticService->getPlacementChangeComparedToDayBefore($simulator),
        ]);
    }

    #[Route('/{simulator}/chart/show', name: 'app_tipico_simulation_chart_show')]
    public function showChart(Simulator $simulator): Response
    {
        $chart = $this->simulationStatisticService->getCashBoxChart($simulator);

        return $this->render('tipico_simulation/chart_show.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/weekday', name: 'app_tipico_weekday')]
    public function weekDay(): Response
    {
        $topWeekDaySimulators = $this->simulationStatisticService->findTopSimulatorsByWeekday();

        return $this->render('tipico_simulation/weekday.html.twig', [
            'topWeekDaySimulators' => $topWeekDaySimulators,
        ]);
    }

    #[Route('/weekday/{simulator}/detail', name: 'app_tipico_weekday_detail')]
    public function weekDayDetail(Simulator $simulator): Response
    {
        $statistics = $this->simulationStatisticService->getWeekdayStatisticForSimulator($simulator);


        return $this->render('tipico_simulation/weekday_detail.html.twig', [
            'statistics' => $statistics,
            'simulator' => $simulator,
        ]);
    }
}
