<?php

declare(strict_types=1);

namespace App\Service\Analysis;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Diagramme für /analysis aus dem gespeicherten Analyse-Stand. Renditen werden in Prozent übergeben, weil sich
 * Achsen-Formatierer aus PHP nicht an Chart.js durchreichen lassen.
 */
final class AnalysisChartService
{
    private const COLOR_POSITIVE = '#3fb27f';
    private const COLOR_NEGATIVE = '#e5566a';
    private const COLOR_ACCENT = '#e0b33a';
    private const COLOR_MUTED = '#93a0b8';

    public function __construct(private readonly ChartBuilderInterface $chartBuilder)
    {
    }

    /**
     * @param list<array{month: string, matches: int}> $months
     */
    public function monthlyMatches(array $months): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_column($months, 'month'),
            'datasets' => [
                [
                    'label' => 'Matches',
                    'backgroundColor' => self::COLOR_ACCENT,
                    'data' => array_column($months, 'matches'),
                ]
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ]);

        return $chart;
    }

    /**
     * @param list<array<string, mixed>> $buckets
     */
    public function oddBuckets(array $buckets): Chart
    {
        $roi = array_map(static fn(array $b): ?float => self::percent($b['roi']), $buckets);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_column($buckets, 'label'),
            'datasets' => [
                [
                    'label' => 'Return in %',
                    'backgroundColor' => array_map(self::signColor(...), $roi),
                    'data' => $roi,
                ]
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['title' => ['display' => true, 'text' => 'Return in %']]],
        ]);

        return $chart;
    }

    /**
     * @param list<array{month: string, placements: int, roi: float|null}> $months
     */
    public function placementMonths(array $months): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => array_column($months, 'month'),
            'datasets' => [
                [
                    'label' => 'Return of all simulator bets in %',
                    'borderColor' => self::COLOR_NEGATIVE,
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'tension' => 0.25,
                    'data' => array_map(static fn(array $m): ?float => self::percent($m['roi']), $months),
                ]
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['suggestedMax' => 0, 'title' => ['display' => true, 'text' => 'Return in %']]],
        ]);

        return $chart;
    }

    /**
     * @param list<array<string, mixed>> $deciles
     */
    public function persistence(array $deciles, string $firstLabel, string $secondLabel): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_map(static fn(array $d): string => 'Decile ' . $d['decile'], $deciles),
            'datasets' => [
                [
                    'label' => $firstLabel,
                    'backgroundColor' => self::COLOR_MUTED,
                    'data' => array_map(static fn(array $d): ?float => self::percent($d['before']), $deciles),
                ],
                [
                    'label' => $secondLabel,
                    'backgroundColor' => self::COLOR_NEGATIVE,
                    'data' => array_map(static fn(array $d): ?float => self::percent($d['after']), $deciles),
                ],
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'scales' => ['y' => ['title' => ['display' => true, 'text' => 'Return in %']]],
        ]);

        return $chart;
    }

    private static function percent(?float $value): ?float
    {
        return $value === null ? null : round($value * 100, 2);
    }

    private static function signColor(?float $value): string
    {
        return ($value ?? 0) >= 0 ? self::COLOR_POSITIVE : self::COLOR_NEGATIVE;
    }
}
