<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Analysis\AnalysisChartService;
use App\Service\Analysis\AnalysisReportService;
use App\Service\Analysis\Message\RefreshMarketReportMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/analysis')]
final class AnalysisController extends AbstractController
{
    public function __construct(
        private readonly AnalysisReportService $analysisReportService,
        private readonly AnalysisChartService $analysisChartService,
    ) {
    }

    #[Route('', name: 'app_analysis', methods: ['GET'])]
    public function index(): Response
    {
        $snapshot = $this->analysisReportService->getMarketReport();
        if ($snapshot === null) {
            return $this->render('analysis/index.html.twig', ['snapshot' => null, 'report' => null, 'charts' => []]);
        }

        $report = $snapshot->getData();
        $charts = [
            'monthlyMatches' => $this->analysisChartService->monthlyMatches($report['monthlyMatches']),
            'oddBuckets' => $this->analysisChartService->oddBuckets($report['oddBuckets']),
            'placementMonths' => $this->analysisChartService->placementMonths($report['placementMonths']),
        ];
        $persistence = $report['persistence'];
        if ($persistence !== null && $persistence['deciles'] !== []) {
            $charts['persistence'] = $this->analysisChartService->persistence(
                $persistence['deciles'],
                sprintf('%s – %s', ...$persistence['firstPeriod']),
                sprintf('%s – %s', ...$persistence['secondPeriod'])
            );
        }

        return $this->render('analysis/index.html.twig', [
            'snapshot' => $snapshot,
            'report' => $report,
            'charts' => $charts,
        ]);
    }

    #[Route('/refresh', name: 'app_analysis_refresh', methods: ['POST'])]
    public function refresh(Request $request, MessageBusInterface $messageBus): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('analysis_refresh', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'The form has expired. Please try again.');

            return $this->redirectToRoute('app_analysis');
        }

        $messageBus->dispatch(new RefreshMarketReportMessage());
        $this->addFlash('info', 'Recalculation started. It takes a minute or two; reload the page afterwards.');

        return $this->redirectToRoute('app_analysis');
    }
}
