<?php

namespace App\Controller;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterType;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Duplication\SimulatorDuplicationData;
use App\Service\Tipico\Duplication\SimulatorDuplicationService;
use App\Service\Tipico\Duplication\SimulatorDuplicationType;
use App\Service\Tipico\Message\ProcessSimulatorMessage;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use App\Service\Tipico\SimulationStatisticService;
use App\Service\Tipico\Statistic\DetailStatisticService;
use App\Service\Tipico\Suggestion\BetPlacementSuggestionContainer;
use App\Service\Tipico\Suggestion\PlacementSuggestion;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipico/simulation')]
class TipicoSimulationController extends AbstractController
{
    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly SimulationStatisticService $simulationStatisticService,
        private readonly MessageBusInterface $messageBus,
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
                return $tipicoBet->getStartAtTimeStamp() / 1000 > $current->getTimestamp();
            }
        );
        $open = count($open);

//        $lastWeekStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(7);
//        $beforeLastWeekStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(14, 7);
//        $lastSameWeekDayStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(7, 6);
//        $yesterdayStatistic = $this->simulationStatisticService->getTopSimulatorsOfLastDays(1);
//        $currentDayStatistic = $this->simulationStatisticService->getTopSimulatorsOfCurrentDay();
//        $cashBoxChart = $this->simulationStatisticService->getSimulatorCashBoxDistributionChart();
//        $distribution = $this->simulationStatisticService->getActiveSimulators();


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
//            'lastWeekStatistic' => $lastWeekStatistic,
//            'beforeLastWeekStatistic' => $beforeLastWeekStatistic,
//            'lastSameWeekDayStatistic' => $lastSameWeekDayStatistic,
//            'yesterdayStatistic' => $yesterdayStatistic,
//            'currentDayStatistic' => $currentDayStatistic,
//            'cashBoxChart' => $cashBoxChart,
//            'totalSimulators' => $distribution['total'],
//            'inactiveSimulators' => $distribution['inactive'],
//            'activeSimulators' => $distribution['active'],
//            'inWinSimulators' => $distribution['inWin'],
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

//        $defaultData = [];
//        $form = $this->createFormBuilder($defaultData)
//            ->add('excludeNegative', CheckboxType::class, ['attr' => ['checked' => 1], 'required' => false])
//            ->add('variant', ChoiceType::class, ['multiple' => true, 'choices' => $choices])
//            ->add('filter', SubmitType::class)
//            ->getForm();

        $filter = new SimulatorFilterData();
        $form = $this->createForm(SimulatorFilterType::class, $filter);

        $data = ['excludeNegative' => true];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filter = $form->getData();
        }
        $filter->setMaxResults(30);
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
        if ($strategy->getIdentifier() === OverUnderStrategy::IDENT) {
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

    #[Route('/{simulator}/statistic', name: 'app_tipico_simulation_statistic')]
    public function statistic(Simulator $simulator, DetailStatisticService $statisticService): Response
    {
        $dto = $statisticService->generateDetailStatisticForSimulator($simulator);

        return $this->render('tipico_simulation/statistic.html.twig', [
            'simulator' => $simulator,
            'dto' => $dto
        ]);
    }

    #[Route('/{simulator}/duplicate', name: 'app_tipico_simulation_duplicate')]
    public function duplicate(
        Request $request,
        Simulator $simulator,
        SimulatorDuplicationService $duplicationService
    ): Response {
        $data = new SimulatorDuplicationData();
        $form = $this->createForm(SimulatorDuplicationType::class, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SimulatorDuplicationData $data */
            $data = $form->getData();
            $weekDays = array_map(
                function (int $value) {
                    return Weekday::from($value);
                },
                $data->getWeekdays()
            );

            $simulator = $duplicationService->duplicateSimulatorAndLimitToWeekdays($simulator, $weekDays);

            $message = new ProcessSimulatorMessage($simulator->getId());
            $this->messageBus->dispatch($message);
        }

        return $this->render('tipico_simulation/duplicate.html.twig', [
            'simulator' => $simulator,
            'form' => $form,
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
        if ($strategy->getIdentifier() === OverUnderStrategy::IDENT) {
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
        $topWeekDaySimulators = $this->simulationStatisticService->findTopSimulatorsByWeekday(50);

        return $this->render('tipico_simulation/weekday.html.twig', [
            'topWeekDaySimulators' => $topWeekDaySimulators,
        ]);
    }

    #[Route('/weekday/suggestions', name: 'app_tipico_weekday_suggestions')]
    public function weekDaySuggestions(Request $request): Response
    {
        $usedSimulators = $request->query->getInt('sim', 100); // Default to 100 if not sent in the request
        $cashBoxMin = (float)$request->query->getInt('cash', 140.0);

        $topWeekDaySimulators = $this->simulationStatisticService->findTopSimulatorsByWeekday(
            $usedSimulators,
            $cashBoxMin
        );

        $from = new DateTime();
        $from->setTime(0, 0);

        $until = clone $from;
        $until->modify('+1 day');
        $until->setTime(0, 0);

        $betPlacementSuggestions = new BetPlacementSuggestionContainer();
        $weekDay = WeekDay::from((new DateTime())->format('N'));

        foreach ($topWeekDaySimulators as $simulator) {
            $strategy = $simulator->getStrategy();
            $parameters = json_decode($strategy->getParameters(), true);

            $nextPlacements = $this->simulationStatisticService->getUpcomingEventsForSimulator($simulator, 200);
            foreach ($nextPlacements as $nextPlacement) {
                $betPlacementSuggestion = $betPlacementSuggestions->getSuggestionByBet($nextPlacement);
                $betPlacementSuggestion->setWeekday($weekDay);

                $simulatorSuggestion = new PlacementSuggestion();


                $simulatorSuggestion->setTargetBetOn(
                    BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON])
                );
                if ($strategy->getIdentifier() === OverUnderStrategy::IDENT) {
                    $simulatorSuggestion->setTargetValue($parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE]);
                }
                $simulatorSuggestion->setSimulatorIdent($simulator->getIdentifier());
                $simulatorSuggestion->setSimulatorId($simulator->getId());
                $simulatorSuggestion->setSimulator($simulator);

                $betPlacementSuggestion->addPlacementSuggestions($simulatorSuggestion);
            }
        }

        return $this->render('tipico_simulation/weekday_suggestion.html.twig', [
            'betPlacementSuggestionsContainer' => $betPlacementSuggestions,
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

    /**
     * @param array<TipicoBet> $suggestions
     */
    public function categorizeSuggestions(array $suggestions): array
    {
    }
}
