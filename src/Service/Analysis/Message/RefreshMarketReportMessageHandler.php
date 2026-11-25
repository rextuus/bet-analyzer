<?php

declare(strict_types=1);

namespace App\Service\Analysis\Message;

use App\Service\Analysis\AnalysisReportService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefreshMarketReportMessageHandler
{
    public function __construct(private readonly AnalysisReportService $analysisReportService)
    {
    }

    public function __invoke(RefreshMarketReportMessage $message): void
    {
        $this->analysisReportService->refreshMarketReport();
    }
}
