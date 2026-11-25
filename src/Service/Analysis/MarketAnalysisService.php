<?php

declare(strict_types=1);

namespace App\Service\Analysis;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;

/**
 * Wertet alle gesammelten Partien, Quoten und Simulator-Platzierungen aus: Datenlage, Rendite einfacher Regeln je
 * Markt, Ergebnis der Simulatoren und ob gute Simulatoren gut bleiben.
 *
 * Renditen gelten immer für 1 Einsatz je Wette (Gewinn = Quote − 1, Verlust = −1). „Laut Quote“ ist die
 * Wahrscheinlichkeit aus der Quote mit herausgerechneter Marge.
 *
 * Läuft über alle Platzierungen (Millionen Zeilen) und braucht einige Sekunden. Darum nur über
 * AnalysisReportService aufrufen, das das Ergebnis zwischenspeichert.
 */
final class MarketAnalysisService
{
    /** Mindestzahl Wetten je Hälfte, damit ein Simulator in den Vorher-/Nachher-Vergleich eingeht. */
    public const MIN_PLACEMENTS_PER_HALF = 50;

    /** Partien ohne Ergebnis, deren Anstoß länger her ist, gelten als „nie nachgetragen“. */
    private const RESULT_GRACE_HOURS = 24;

    private const VALID_THREE_WAY = 'b.finished = 1 AND b.odd_home > 1 AND b.odd_draw > 1 AND b.odd_away > 1';

    /** Klassen für die Quotenhöhe als [Untergrenze, Bezeichnung], aufsteigend (Float-Schlüssel gehen in PHP nicht). */
    private const ODD_BUCKETS = [
        [1.0, '< 1.30'],
        [1.3, '1.30 – 1.60'],
        [1.6, '1.60 – 2.00'],
        [2.0, '2.00 – 3.00'],
        [3.0, '3.00 – 5.00'],
        [5.0, '≥ 5.00'],
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function compute(): array
    {
        return [
            'coverage' => $this->coverage(),
            'monthlyMatches' => $this->monthlyMatches(),
            'threeWay' => $this->threeWay(),
            'oddBuckets' => $this->oddBuckets(),
            'overUnder' => $this->overUnder(),
            'bothTeamsScore' => $this->bothTeamsScore(),
            'headToHead' => $this->headToHead(),
            'simulators' => $this->simulators(),
            'strategies' => $this->strategies(),
            'topSimulators' => $this->topSimulators(),
            'placementMonths' => $this->placementMonths(),
            'persistence' => $this->persistence(),
            'lineShopping' => $this->lineShopping(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function coverage(): array
    {
        $graceLimit = (time() - self::RESULT_GRACE_HOURS * 3600) * 1000;
        $now = time() * 1000;

        $row = $this->connection->fetchAssociative(
            'SELECT
                COUNT(*) AS total,
                COALESCE(SUM(b.finished = 1), 0) AS finished,
                COALESCE(SUM(b.finished = 0 AND b.start_at_time_stamp < :grace), 0) AS missing_result,
                COALESCE(SUM(b.finished = 0 AND b.start_at_time_stamp >= :grace AND b.start_at_time_stamp < :now), 0) AS pending_result,
                COALESCE(SUM(b.start_at_time_stamp >= :now), 0) AS upcoming,
                MIN(b.start_at_time_stamp) AS first_match,
                MAX(b.start_at_time_stamp) AS last_match,
                COALESCE(SUM(b.odd_home > 1 AND b.odd_draw > 1 AND b.odd_away > 1), 0) AS three_way,
                COALESCE(SUM(EXISTS(SELECT 1 FROM tipico_over_under_odd o WHERE o.bet_id = b.id)), 0) AS over_under,
                COALESCE(SUM(EXISTS(SELECT 1 FROM tipico_both_teams_score_odd o WHERE o.bet_id = b.id)), 0) AS both_teams_score,
                COALESCE(SUM(EXISTS(SELECT 1 FROM tipico_head_to_head_odd o WHERE o.bet_id = b.id)), 0) AS head_to_head,
                COALESCE(SUM(b.corresponded_betano_bet_id IS NOT NULL), 0) AS betano,
                COALESCE(SUM(EXISTS(SELECT 1 FROM bwin_bet o WHERE o.tipico_bet_id = b.id)), 0) AS bwin,
                COALESCE(SUM(b.finished = 1 AND b.result = \'1\'), 0) AS result_home,
                COALESCE(SUM(b.finished = 1 AND b.result = \'X\'), 0) AS result_draw,
                COALESCE(SUM(b.finished = 1 AND b.result = \'2\'), 0) AS result_away
            FROM tipico_bet b',
            ['grace' => $graceLimit, 'now' => $now]
        );

        $margin = $this->connection->fetchOne(
            'SELECT AVG(1 / b.odd_home + 1 / b.odd_draw + 1 / b.odd_away) - 1 FROM tipico_bet b
             WHERE b.odd_home > 1 AND b.odd_draw > 1 AND b.odd_away > 1'
        );

        return [
            'total' => (int) $row['total'],
            'finished' => (int) $row['finished'],
            'missingResult' => (int) $row['missing_result'],
            'pendingResult' => (int) $row['pending_result'],
            'upcoming' => (int) $row['upcoming'],
            'firstMatch' => $this->formatTimestamp($row['first_match']),
            'lastMatch' => $this->formatTimestamp($row['last_match']),
            'markets' => [
                ['label' => '1X2 (Tipico)', 'count' => (int) $row['three_way']],
                ['label' => 'Over/under', 'count' => (int) $row['over_under']],
                ['label' => 'Both teams score', 'count' => (int) $row['both_teams_score']],
                ['label' => 'Head-to-head (draw = stake back)', 'count' => (int) $row['head_to_head']],
                ['label' => 'Betano comparison odds', 'count' => (int) $row['betano']],
                ['label' => 'Bwin comparison odds', 'count' => (int) $row['bwin']],
            ],
            'results' => [
                'home' => (int) $row['result_home'],
                'draw' => (int) $row['result_draw'],
                'away' => (int) $row['result_away'],
            ],
            'margin' => $margin === null ? null : (float) $margin,
        ];
    }

    /**
     * @return list<array{month: string, matches: int}>
     */
    private function monthlyMatches(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT DATE_FORMAT(FROM_UNIXTIME(b.start_at_time_stamp / 1000), \'%Y-%m\') AS month, COUNT(*) AS matches
             FROM tipico_bet b GROUP BY month ORDER BY month'
        );

        return array_map(
            static fn(array $r): array => ['month' => (string) $r['month'], 'matches' => (int) $r['matches']],
            $rows
        );
    }

    /**
     * Immer auf denselben Ausgang setzen.
     *
     * @return array<string, mixed>
     */
    private function threeWay(): array
    {
        $fair = '(1 / b.odd_home + 1 / b.odd_draw + 1 / b.odd_away)';
        $row = $this->connection->fetchAssociative(
            "SELECT
                COUNT(*) AS n,
                AVG(b.result = '1') AS hit_home, AVG(1 / b.odd_home / $fair) AS implied_home,
                AVG(IF(b.result = '1', b.odd_home, 0)) - 1 AS roi_home,
                AVG(b.result = 'X') AS hit_draw, AVG(1 / b.odd_draw / $fair) AS implied_draw,
                AVG(IF(b.result = 'X', b.odd_draw, 0)) - 1 AS roi_draw,
                AVG(b.result = '2') AS hit_away, AVG(1 / b.odd_away / $fair) AS implied_away,
                AVG(IF(b.result = '2', b.odd_away, 0)) - 1 AS roi_away,
                AVG(IF(b.odd_home <= b.odd_away, b.result = '1', b.result = '2')) AS hit_favorite,
                AVG(IF(b.odd_home <= b.odd_away, 1 / b.odd_home, 1 / b.odd_away) / $fair) AS implied_favorite,
                AVG(IF(b.odd_home <= b.odd_away, IF(b.result = '1', b.odd_home, 0), IF(b.result = '2', b.odd_away, 0))) - 1 AS roi_favorite,
                AVG(IF(b.odd_home > b.odd_away, b.result = '1', b.result = '2')) AS hit_underdog,
                AVG(IF(b.odd_home > b.odd_away, 1 / b.odd_home, 1 / b.odd_away) / $fair) AS implied_underdog,
                AVG(IF(b.odd_home > b.odd_away, IF(b.result = '1', b.odd_home, 0), IF(b.result = '2', b.odd_away, 0))) - 1 AS roi_underdog
            FROM tipico_bet b WHERE " . self::VALID_THREE_WAY
        );

        $rows = [];
        foreach (
            [
                'home' => 'Home win',
                'draw' => 'Draw',
                'away' => 'Away win',
                'favorite' => 'Favorite (lower win odd)',
                'underdog' => 'Underdog'
            ] as $key => $label
        ) {
            $rows[] = [
                'label' => $label,
                'hit' => $this->float($row["hit_$key"]),
                'implied' => $this->float($row["implied_$key"]),
                'roi' => $this->float($row["roi_$key"]),
            ];
        }

        return ['bets' => (int) $row['n'], 'rows' => $rows];
    }

    /**
     * Alle drei Ausgänge einer Partie als Einzelwetten, nach Quotenhöhe gruppiert.
     *
     * @return list<array<string, mixed>>
     */
    private function oddBuckets(): array
    {
        $parts = [];
        foreach (['odd_home' => '1', 'odd_draw' => 'X', 'odd_away' => '2'] as $column => $result) {
            $parts[] = sprintf(
                'SELECT b.%1$s AS odd, b.result = \'%2$s\' AS won, %3$s AS bucket FROM tipico_bet b WHERE %4$s',
                $column,
                $result,
                $this->bucketCase('b.' . $column),
                self::VALID_THREE_WAY
            );
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT bucket, COUNT(*) AS n, AVG(won) AS hit, AVG(1 / odd) AS implied, AVG(won * odd) - 1 AS roi
             FROM (' . implode(' UNION ALL ', $parts) . ') x GROUP BY bucket ORDER BY bucket'
        );

        $labels = array_column(self::ODD_BUCKETS, 1);

        return array_map(
            fn(array $r): array => [
                'label' => $labels[(int) $r['bucket']],
                'bets' => (int) $r['n'],
                'hit' => $this->float($r['hit']),
                'implied' => $this->float($r['implied']),
                'roi' => $this->float($r['roi']),
            ],
            $rows
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function overUnder(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT o.target_value AS target, COUNT(*) AS n,
                AVG(b.end_score_home + b.end_score_away > o.target_value) AS hit_over,
                AVG(1 / o.over_value / (1 / o.over_value + 1 / o.under_value)) AS implied_over,
                AVG(IF(b.end_score_home + b.end_score_away > o.target_value, o.over_value, 0)) - 1 AS roi_over,
                AVG(IF(b.end_score_home + b.end_score_away < o.target_value, o.under_value, 0)) - 1 AS roi_under,
                AVG(1 / o.over_value + 1 / o.under_value) - 1 AS margin
             FROM tipico_over_under_odd o JOIN tipico_bet b ON b.id = o.bet_id
             WHERE b.finished = 1 AND o.over_value > 1 AND o.under_value > 1
             GROUP BY o.target_value HAVING n >= 1000 ORDER BY o.target_value'
        );

        return array_map(
            fn(array $r): array => [
                'target' => (float) $r['target'],
                'bets' => (int) $r['n'],
                'hitOver' => $this->float($r['hit_over']),
                'impliedOver' => $this->float($r['implied_over']),
                'roiOver' => $this->float($r['roi_over']),
                'roiUnder' => $this->float($r['roi_under']),
                'margin' => $this->float($r['margin']),
            ],
            $rows
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function bothTeamsScore(): array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT COUNT(*) AS n,
                AVG(b.end_score_home > 0 AND b.end_score_away > 0) AS hit_yes,
                AVG(1 / o.condition_true_value / (1 / o.condition_true_value + 1 / o.condition_false_value)) AS implied_yes,
                AVG(IF(b.end_score_home > 0 AND b.end_score_away > 0, o.condition_true_value, 0)) - 1 AS roi_yes,
                AVG(IF(b.end_score_home > 0 AND b.end_score_away > 0, 0, o.condition_false_value)) - 1 AS roi_no,
                AVG(1 / o.condition_true_value + 1 / o.condition_false_value) - 1 AS margin
             FROM tipico_both_teams_score_odd o JOIN tipico_bet b ON b.id = o.bet_id
             WHERE b.finished = 1 AND o.condition_true_value > 1 AND o.condition_false_value > 1'
        );

        return [
            'bets' => (int) $row['n'],
            'hitYes' => $this->float($row['hit_yes']),
            'impliedYes' => $this->float($row['implied_yes']),
            'roiYes' => $this->float($row['roi_yes']),
            'roiNo' => $this->float($row['roi_no']),
            'margin' => $this->float($row['margin']),
        ];
    }

    /**
     * Head-to-Head wie im HeadToHeadStrategy-Prozessor: Bei Remis gibt es den Einsatz zurück.
     *
     * @return array<string, mixed>
     */
    private function headToHead(): array
    {
        $row = $this->connection->fetchAssociative(
            "SELECT COUNT(*) AS n,
                AVG(CASE b.result WHEN '1' THEN o.home_team_value WHEN 'X' THEN 1 ELSE 0 END) - 1 AS roi_home,
                AVG(CASE b.result WHEN '2' THEN o.away_team_value WHEN 'X' THEN 1 ELSE 0 END) - 1 AS roi_away
             FROM tipico_head_to_head_odd o JOIN tipico_bet b ON b.id = o.bet_id
             WHERE b.finished = 1 AND o.home_team_value > 1 AND o.away_team_value > 1"
        );

        return [
            'bets' => (int) $row['n'],
            'roiHome' => $this->float($row['roi_home']),
            'roiAway' => $this->float($row['roi_away']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function simulators(): array
    {
        $sim = $this->connection->fetchAssociative(
            'SELECT COUNT(*) AS total, COALESCE(SUM(cash_box > 100), 0) AS profit, COALESCE(SUM(cash_box < 100), 0) AS loss
             FROM simulator'
        );
        $placements = $this->connection->fetchAssociative(
            'SELECT COUNT(*) AS n, COALESCE(SUM(input), 0) AS stake, COALESCE(SUM(IF(won = 1, input * value, 0)) - SUM(input), 0) AS net,
                MIN(created) AS first, MAX(created) AS last, COUNT(DISTINCT simulator_id) AS active
             FROM tipico_placement'
        );

        $stake = (float) $placements['stake'];

        return [
            'total' => (int) $sim['total'],
            'active' => (int) $placements['active'],
            'inProfit' => (int) $sim['profit'],
            'inLoss' => (int) $sim['loss'],
            'placements' => (int) $placements['n'],
            'stake' => $stake,
            'net' => (float) $placements['net'],
            'roi' => $stake > 0 ? (float) $placements['net'] / $stake : null,
            'firstPlacement' => $placements['first'],
            'lastPlacement' => $placements['last'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function strategies(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT st.identifier AS strategy, COUNT(*) AS n, SUM(s.cash_box > 100) AS profit, AVG(s.cash_box - 100) AS avg_result
             FROM simulator s JOIN simulation_strategy st ON st.id = s.strategy_id
             GROUP BY st.identifier ORDER BY n DESC'
        );

        return array_map(
            fn(array $r): array => [
                'strategy' => (string) $r['strategy'],
                'simulators' => (int) $r['n'],
                'inProfit' => (int) $r['profit'],
                'averageResult' => $this->float($r['avg_result']),
            ],
            $rows
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topSimulators(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT s.id, s.identifier, st.identifier AS strategy, s.cash_box,
                (SELECT COUNT(*) FROM tipico_placement p WHERE p.simulator_id = s.id) AS placements
             FROM simulator s JOIN simulation_strategy st ON st.id = s.strategy_id
             ORDER BY s.cash_box DESC LIMIT 10'
        );

        return array_map(
            static fn(array $r): array => [
                'id' => (int) $r['id'],
                'identifier' => (string) $r['identifier'],
                'strategy' => (string) $r['strategy'],
                'cashBox' => (float) $r['cash_box'],
                'placements' => (int) $r['placements'],
            ],
            $rows
        );
    }

    /**
     * @return list<array{month: string, placements: int, roi: float|null}>
     */
    private function placementMonths(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT DATE_FORMAT(created, \'%Y-%m\') AS month, COUNT(*) AS n,
                (SUM(IF(won = 1, input * value, 0)) - SUM(input)) / NULLIF(SUM(input), 0) AS roi
             FROM tipico_placement GROUP BY month ORDER BY month'
        );

        return array_map(
            fn(array $r): array => [
                'month' => (string) $r['month'],
                'placements' => (int) $r['n'],
                'roi' => $this->float($r['roi'])
            ],
            $rows
        );
    }

    /**
     * Bleiben gute Simulatoren gut? Der Zeitraum aller Platzierungen wird in der Mitte geteilt. Simulatoren mit
     * genug Wetten in beiden Hälften werden nach ihrer Rendite in der ersten Hälfte in Zehntel sortiert; verglichen
     * wird mit der Rendite derselben Simulatoren in der zweiten Hälfte.
     *
     * @return array<string, mixed>|null
     */
    private function persistence(): ?array
    {
        $range = $this->connection->fetchAssociative(
            'SELECT MIN(created) AS first, MAX(created) AS last FROM tipico_placement'
        );
        if ($range['first'] === null) {
            return null;
        }

        $first = new DateTimeImmutable($range['first']);
        $last = new DateTimeImmutable($range['last']);
        $split = $first->setTimestamp(intdiv($first->getTimestamp() + $last->getTimestamp(), 2))->setTime(0, 0);

        $rows = $this->connection->fetchAllAssociative(
            'SELECT simulator_id,
                SUM(created < :split) AS n1, SUM(IF(created < :split, IF(won = 1, input * value, 0) - input, 0)) AS net1,
                SUM(IF(created < :split, input, 0)) AS stake1,
                SUM(created >= :split) AS n2, SUM(IF(created >= :split, IF(won = 1, input * value, 0) - input, 0)) AS net2,
                SUM(IF(created >= :split, input, 0)) AS stake2
             FROM tipico_placement WHERE simulator_id IS NOT NULL
             GROUP BY simulator_id HAVING n1 >= :min AND n2 >= :min AND stake1 > 0 AND stake2 > 0',
            ['split' => $split->format('Y-m-d H:i:s'), 'min' => self::MIN_PLACEMENTS_PER_HALF]
        );

        $simulators = array_map(
            static fn(array $r): array => [
                'before' => (float) $r['net1'] / (float) $r['stake1'],
                'after' => (float) $r['net2'] / (float) $r['stake2'],
            ],
            $rows
        );
        usort($simulators, static fn(array $a, array $b): int => $b['before'] <=> $a['before']);

        $count = count($simulators);
        $result = [
            'firstPeriod' => [$first->format('Y-m-d'), $split->modify('-1 day')->format('Y-m-d')],
            'secondPeriod' => [$split->format('Y-m-d'), $last->format('Y-m-d')],
            'minPlacements' => self::MIN_PLACEMENTS_PER_HALF,
            'simulators' => $count,
            'profitBefore' => count(array_filter($simulators, static fn(array $s): bool => $s['before'] > 0)),
            'profitAfter' => count(array_filter($simulators, static fn(array $s): bool => $s['after'] > 0)),
            'profitBoth' => count(
                array_filter($simulators, static fn(array $s): bool => $s['before'] > 0 && $s['after'] > 0)
            ),
            'deciles' => [],
            'top50' => null,
        ];
        if ($count < 10) {
            return $result;
        }

        for ($decile = 0; $decile < 10; $decile++) {
            $slice = array_slice(
                $simulators,
                intdiv($decile * $count, 10),
                intdiv(($decile + 1) * $count, 10) - intdiv($decile * $count, 10)
            );
            $result['deciles'][] = [
                'decile' => $decile + 1,
                'simulators' => count($slice),
                'before' => $this->average(array_column($slice, 'before')),
                'after' => $this->average(array_column($slice, 'after')),
                'shareProfitAfter' => count(array_filter($slice, static fn(array $s): bool => $s['after'] > 0)) / count(
                        $slice
                    ),
            ];
        }

        $top = array_slice($simulators, 0, min(50, $count));
        $result['top50'] = [
            'simulators' => count($top),
            'before' => $this->average(array_column($top, 'before')),
            'after' => $this->average(array_column($top, 'after')),
        ];

        return $result;
    }

    /**
     * Was bringt es, für jede Partie die bessere Quote zweier Buchmacher zu nehmen?
     *
     * @return list<array<string, mixed>>
     */
    private function lineShopping(): array
    {
        $sources = [
            'betano' => [
                'join' => 'JOIN betano_bet c ON c.id = b.corresponded_betano_bet_id',
                'label' => 'Betano',
            ],
            'bwin' => [
                'join' => 'JOIN bwin_bet c ON c.tipico_bet_id = b.id',
                'label' => 'Bwin',
            ],
        ];

        $result = [];
        foreach ($sources as $source) {
            $where = 'WHERE ' . self::VALID_THREE_WAY . ' AND c.odd_home > 1 AND c.odd_draw > 1 AND c.odd_away > 1';
            $variants = [
                'Tipico' => ['b.odd_home', 'b.odd_draw', 'b.odd_away'],
                $source['label'] => ['c.odd_home', 'c.odd_draw', 'c.odd_away'],
                'Best of both' => [
                    'GREATEST(b.odd_home, c.odd_home)',
                    'GREATEST(b.odd_draw, c.odd_draw)',
                    'GREATEST(b.odd_away, c.odd_away)'
                ],
            ];

            $rows = [];
            foreach ($variants as $label => [$home, $draw, $away]) {
                $row = $this->connection->fetchAssociative(
                    "SELECT COUNT(*) AS n,
                        AVG(1 / $home + 1 / $draw + 1 / $away) - 1 AS margin,
                        AVG(IF(b.result = '1', $home, 0)) - 1 AS roi_home,
                        AVG(IF(b.result = 'X', $draw, 0)) - 1 AS roi_draw,
                        AVG(IF(b.result = '2', $away, 0)) - 1 AS roi_away,
                        COALESCE(SUM(1 / $home + 1 / $draw + 1 / $away < 1), 0) AS surebets,
                        AVG(IF(1 / $home + 1 / $draw + 1 / $away < 1, 1 - (1 / $home + 1 / $draw + 1 / $away), NULL)) AS surebet_margin
                     FROM tipico_bet b {$source['join']} $where"
                );
                $rows[] = [
                    'label' => $label,
                    'margin' => $this->float($row['margin']),
                    'roiHome' => $this->float($row['roi_home']),
                    'roiDraw' => $this->float($row['roi_draw']),
                    'roiAway' => $this->float($row['roi_away']),
                    'surebets' => (int) $row['surebets'],
                    'surebetMargin' => $this->float($row['surebet_margin']),
                    'matches' => (int) $row['n'],
                ];
            }

            $range = $this->connection->fetchAssociative(
                "SELECT MIN(b.start_at_time_stamp) AS first, MAX(b.start_at_time_stamp) AS last FROM tipico_bet b {$source['join']} $where"
            );

            $result[] = [
                'provider' => $source['label'],
                'matches' => $rows[0]['matches'],
                'firstMatch' => $this->formatTimestamp($range['first']),
                'lastMatch' => $this->formatTimestamp($range['last']),
                'rows' => $rows,
            ];
        }

        return $result;
    }

    private function bucketCase(string $column): string
    {
        $bounds = array_column(self::ODD_BUCKETS, 0);
        $case = 'CASE';
        for ($i = count($bounds) - 1; $i > 0; $i--) {
            $case .= sprintf(' WHEN %s >= %F THEN %d', $column, $bounds[$i], $i);
        }

        return $case . ' ELSE 0 END';
    }

    private function formatTimestamp(mixed $milliseconds): ?string
    {
        if ($milliseconds === null) {
            return null;
        }

        return (new DateTimeImmutable())->setTimestamp(intdiv((int) $milliseconds, 1000))->format('Y-m-d H:i');
    }

    private function float(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    /**
     * @param list<float> $values
     */
    private function average(array $values): ?float
    {
        return $values === [] ? null : array_sum($values) / count($values);
    }
}
