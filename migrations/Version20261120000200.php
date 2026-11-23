<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Indizes für die häufigsten Abfragen: Tagesspiele (Startzeit), offene Ergebnisse (finished + Startzeit),
 * Abgleich per Tipico-ID und Platzierungen je Simulator und Zeitraum. Bisher gab es außer Primär- und
 * Fremdschlüsseln keine.
 *
 * Achtung: tipico_placement ist die größte Tabelle (Millionen Zeilen). MySQL legt den Index online an
 * (ALGORITHM=INPLACE), das dauert aber – beim Umzug den Dienst migrate einmal von Hand laufen lassen, bevor
 * Komodo deployt (docs/deployment.md).
 */
final class Version20261120000200 extends AbstractMigration
{
    private const INDEXES = [
        'tipico_bet' => [
            'idx_tipico_bet_tipico_id' => '(tipico_id)',
            'idx_tipico_bet_start' => '(start_at_time_stamp)',
            'idx_tipico_bet_finished_start' => '(finished, start_at_time_stamp)',
        ],
        'tipico_placement' => [
            'idx_tipico_placement_simulator_created' => '(simulator_id, created)',
        ],
    ];

    public function getDescription(): string
    {
        return 'Indizes für tipico_bet und tipico_placement';
    }

    public function up(Schema $schema): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            foreach ($indexes as $name => $columns) {
                if (!$schema->getTable($table)->hasIndex($name)) {
                    $this->addSql(sprintf('CREATE INDEX %s ON %s %s', $name, $table, $columns));
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            foreach (array_keys($indexes) as $name) {
                $this->addSql(sprintf('DROP INDEX %s ON %s', $name, $table));
            }
        }
    }
}
