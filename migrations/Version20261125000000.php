<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Tabelle für zwischengespeicherte Auswertungen (Seite /analysis). Ein Eintrag je Auswertung, wird bei jeder
 * Neuberechnung überschrieben.
 */
final class Version20261125000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'analysis_snapshot für die Marktanalyse';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('analysis_snapshot')) {
            return;
        }

        $this->addSql(
            'CREATE TABLE analysis_snapshot (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(64) NOT NULL,
                data JSON NOT NULL,
                computed_at DATETIME NOT NULL,
                duration_ms INT NOT NULL,
                UNIQUE INDEX uniq_analysis_snapshot_name (name),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS analysis_snapshot');
    }
}
