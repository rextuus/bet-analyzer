<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Gleicht eine übernommene Datenbank an die Entities an. Ältere Stände (z. B. der Dump vom 16.08.2024) kennen
 * einige Tabellen noch nicht, und tipico_away_team_id war INT statt BIGINT. Alles ist an Bedingungen geknüpft:
 * Auf einer frischen Datenbank (Ausgangsschema) und auf einem aktuellen Stand passiert nichts.
 */
final class Version20261120000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fehlende Tabellen ergänzen, tipico_away_team_id auf BIGINT';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('betting_provider_backup_file')) {
            $this->addSql(
                'CREATE TABLE betting_provider_backup_file (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL, file_path VARCHAR(255) NOT NULL, is_consumed TINYINT(1) NOT NULL, containing_bets INT NOT NULL, fitted_bets INT NOT NULL, already_fitted_bets INT NOT NULL, non_fitted_bets INT NOT NULL, provider VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
            );
        }
        if (!$schema->hasTable('parameter')) {
            $this->addSql(
                'CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
            );
        }
        if (!$schema->hasTable('simulator_detail_statistic')) {
            $this->addSql(
                'CREATE TABLE simulator_detail_statistic (id INT AUTO_INCREMENT NOT NULL, simulator_id INT DEFAULT NULL, creation_date DATETIME NOT NULL, edited DATETIME DEFAULT NULL, monday_total DOUBLE PRECISION NOT NULL, monday_average DOUBLE PRECISION NOT NULL, tuesday_total DOUBLE PRECISION NOT NULL, tuesday_average DOUBLE PRECISION NOT NULL, wednesday_total DOUBLE PRECISION NOT NULL, wednesday_average DOUBLE PRECISION NOT NULL, thursday_total DOUBLE PRECISION NOT NULL, thursday_average DOUBLE PRECISION NOT NULL, friday_total DOUBLE PRECISION NOT NULL, friday_average DOUBLE PRECISION NOT NULL, saturday_total DOUBLE PRECISION NOT NULL, saturday_average DOUBLE PRECISION NOT NULL, sunday_total DOUBLE PRECISION NOT NULL, sunday_average DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_6A31A9DDE4D71D57 (simulator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
            );
            $this->addSql(
                'ALTER TABLE simulator_detail_statistic ADD CONSTRAINT FK_6A31A9DDE4D71D57 FOREIGN KEY (simulator_id) REFERENCES simulator (id)'
            );
        }
        if (!$schema->hasTable('messenger_messages')) {
            $this->addSql(
                'CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
            );
        }

        if ($schema->hasTable('tipico_bet')
            && Types::BIGINT !== $schema->getTable('tipico_bet')->getColumn('tipico_away_team_id')->getType()->getName()
        ) {
            // Auf einer großen tipico_bet dauert das (Tabelle wird umkopiert) – beim Umzug einplanen.
            $this->addSql('ALTER TABLE tipico_bet CHANGE tipico_away_team_id tipico_away_team_id BIGINT NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Angleichung wird nicht zurückgenommen.');
    }
}
