<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Analysis\AnalysisReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bet-analyzer:analysis:refresh',
    description: 'Recalculate the market analysis shown on /analysis',
)]
final class RefreshAnalysisCommand extends Command
{
    public function __construct(private readonly AnalysisReportService $analysisReportService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $snapshot = $this->analysisReportService->refreshMarketReport();
        $coverage = $snapshot->getData()['coverage'];

        $io->success(
            sprintf(
                'Market analysis updated in %.1f s (%d matches, last match %s).',
                $snapshot->getDurationMs() / 1000,
                $coverage['total'],
                $coverage['lastMatch'] ?? '–'
            )
        );

        return Command::SUCCESS;
    }
}
