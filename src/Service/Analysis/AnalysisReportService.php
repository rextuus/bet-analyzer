<?php

declare(strict_types=1);

namespace App\Service\Analysis;

use App\Entity\BettingProvider\AnalysisSnapshot;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Hält den letzten Stand der Marktanalyse in analysis_snapshot vor. Die Seite liest nur, neu gerechnet wird nachts per
 * Cron (bet-analyzer:analysis:refresh) oder auf Knopfdruck im Worker.
 */
final class AnalysisReportService
{
    public const MARKET_REPORT = 'market_report';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MarketAnalysisService $marketAnalysisService,
    ) {
    }

    public function getMarketReport(): ?AnalysisSnapshot
    {
        return $this->entityManager->getRepository(AnalysisSnapshot::class)->findOneBy(['name' => self::MARKET_REPORT]);
    }

    public function refreshMarketReport(): AnalysisSnapshot
    {
        $started = hrtime(true);
        $data = $this->marketAnalysisService->compute();
        $durationMs = (int) ((hrtime(true) - $started) / 1_000_000);

        $snapshot = $this->getMarketReport() ?? new AnalysisSnapshot(self::MARKET_REPORT);
        $snapshot->update($data, new DateTime(), $durationMs);

        $this->entityManager->persist($snapshot);
        $this->entityManager->flush();

        return $snapshot;
    }
}
