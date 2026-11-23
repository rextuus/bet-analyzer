<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ausgangsschema (Stand 20.11.2026, aus den Entities erzeugt). Bis dahin gab es keine Migrationen; das Schema
 * existierte nur in der Datenbank auf securitas.
 *
 * Bei einer übernommenen Datenbank (Import von securitas) gibt es die Tabellen schon: Dann wird die Migration
 * nichts ausgeführt und sie nur als erledigt vermerkt. Auf einer leeren Datenbank legt sie alles an.
 */
final class Version20261120000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ausgangsschema bet-analyzer (ohne Wirkung, wenn tipico_bet schon existiert)';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('tipico_bet')) {
            // Ohne SQL beenden statt skipIf: Übersprungene Migrationen vermerkt Doctrine nicht als ausgeführt.
            $this->write('Bestehende Datenbank – Ausgangsschema ist schon da.');

            return;
        }

        $this->addSql(
            'CREATE TABLE betano_bet (id INT AUTO_INCREMENT NOT NULL, betano_id INT NOT NULL, home_team_name VARCHAR(255) NOT NULL, away_team_name VARCHAR(255) NOT NULL, start_at_time_stamp BIGINT NOT NULL, odd_home DOUBLE PRECISION NOT NULL, odd_draw DOUBLE PRECISION NOT NULL, odd_away DOUBLE PRECISION NOT NULL, url VARCHAR(255) NOT NULL, sport_radar_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE betting_provider_backup_file (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL, file_path VARCHAR(255) NOT NULL, is_consumed TINYINT(1) NOT NULL, containing_bets INT NOT NULL, fitted_bets INT NOT NULL, already_fitted_bets INT NOT NULL, non_fitted_bets INT NOT NULL, provider VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE bwin_bet (id INT AUTO_INCREMENT NOT NULL, tipico_bet_id INT NOT NULL, bwin_id VARCHAR(255) NOT NULL, home_team_name VARCHAR(255) NOT NULL, away_team_name VARCHAR(255) NOT NULL, start_at_time_stamp BIGINT NOT NULL, odd_home DOUBLE PRECISION NOT NULL, odd_draw DOUBLE PRECISION NOT NULL, odd_away DOUBLE PRECISION NOT NULL, url VARCHAR(255) NOT NULL, sport_radar_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_40052BC9C31990ED (tipico_bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulation_strategy (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, parameters JSON NOT NULL, additional_processing_ident VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulator (id INT AUTO_INCREMENT NOT NULL, strategy_id INT NOT NULL, cash_box DOUBLE PRECISION NOT NULL, identifier VARCHAR(255) NOT NULL, current_in DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_81C34CA7D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulator_tipico_bet (simulator_id INT NOT NULL, tipico_bet_id INT NOT NULL, INDEX IDX_7D1B578CE4D71D57 (simulator_id), INDEX IDX_7D1B578CC31990ED (tipico_bet_id), PRIMARY KEY(simulator_id, tipico_bet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulator_detail_statistic (id INT AUTO_INCREMENT NOT NULL, simulator_id INT DEFAULT NULL, creation_date DATETIME NOT NULL, edited DATETIME DEFAULT NULL, monday_total DOUBLE PRECISION NOT NULL, monday_average DOUBLE PRECISION NOT NULL, tuesday_total DOUBLE PRECISION NOT NULL, tuesday_average DOUBLE PRECISION NOT NULL, wednesday_total DOUBLE PRECISION NOT NULL, wednesday_average DOUBLE PRECISION NOT NULL, thursday_total DOUBLE PRECISION NOT NULL, thursday_average DOUBLE PRECISION NOT NULL, friday_total DOUBLE PRECISION NOT NULL, friday_average DOUBLE PRECISION NOT NULL, saturday_total DOUBLE PRECISION NOT NULL, saturday_average DOUBLE PRECISION NOT NULL, sunday_total DOUBLE PRECISION NOT NULL, sunday_average DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_6A31A9DDE4D71D57 (simulator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulator_favorite_list (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, created DATETIME NOT NULL, total_cash_box DOUBLE PRECISION NOT NULL, bets INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE simulator_favorite_list_simulator (simulator_favorite_list_id INT NOT NULL, simulator_id INT NOT NULL, INDEX IDX_3D4DCF2C7FA78211 (simulator_favorite_list_id), INDEX IDX_3D4DCF2CE4D71D57 (simulator_id), PRIMARY KEY(simulator_favorite_list_id, simulator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_bet (id INT AUTO_INCREMENT NOT NULL, corresponded_betano_bet_id INT DEFAULT NULL, tipico_id INT NOT NULL, tipico_home_team_id INT NOT NULL, tipico_away_team_id BIGINT NOT NULL, home_team_name VARCHAR(255) NOT NULL, away_team_name VARCHAR(255) NOT NULL, start_at_time_stamp BIGINT NOT NULL, odd_home DOUBLE PRECISION NOT NULL, odd_draw DOUBLE PRECISION NOT NULL, odd_away DOUBLE PRECISION NOT NULL, end_score_home INT NOT NULL, end_score_away INT NOT NULL, finished TINYINT(1) NOT NULL, result VARCHAR(255) NOT NULL, sport_radar_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5C4B55D85C2DB310 (corresponded_betano_bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_both_teams_score_odd (id INT AUTO_INCREMENT NOT NULL, bet_id INT NOT NULL, condition_true_value DOUBLE PRECISION NOT NULL, condition_false_value DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_72F8C680D871DC26 (bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_head_to_head_odd (id INT AUTO_INCREMENT NOT NULL, bet_id INT NOT NULL, home_team_value DOUBLE PRECISION NOT NULL, away_team_value DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_B5C4727ED871DC26 (bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_over_under_odd (id INT AUTO_INCREMENT NOT NULL, bet_id INT NOT NULL, over_value DOUBLE PRECISION NOT NULL, under_value DOUBLE PRECISION NOT NULL, target_value DOUBLE PRECISION NOT NULL, INDEX IDX_589F4ACED871DC26 (bet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_placement (id INT AUTO_INCREMENT NOT NULL, simulator_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, won TINYINT(1) NOT NULL, input DOUBLE PRECISION NOT NULL, INDEX IDX_CA84E5D7E4D71D57 (simulator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tipico_placement_tipico_bet (tipico_placement_id INT NOT NULL, tipico_bet_id INT NOT NULL, INDEX IDX_18709411275BA6DF (tipico_placement_id), INDEX IDX_18709411C31990ED (tipico_bet_id), PRIMARY KEY(tipico_placement_id, tipico_bet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE bwin_bet ADD CONSTRAINT FK_40052BC9C31990ED FOREIGN KEY (tipico_bet_id) REFERENCES tipico_bet (id)'
        );
        $this->addSql(
            'ALTER TABLE simulator ADD CONSTRAINT FK_81C34CA7D5CAD932 FOREIGN KEY (strategy_id) REFERENCES simulation_strategy (id)'
        );
        $this->addSql(
            'ALTER TABLE simulator_tipico_bet ADD CONSTRAINT FK_7D1B578CE4D71D57 FOREIGN KEY (simulator_id) REFERENCES simulator (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE simulator_tipico_bet ADD CONSTRAINT FK_7D1B578CC31990ED FOREIGN KEY (tipico_bet_id) REFERENCES tipico_bet (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE simulator_detail_statistic ADD CONSTRAINT FK_6A31A9DDE4D71D57 FOREIGN KEY (simulator_id) REFERENCES simulator (id)'
        );
        $this->addSql(
            'ALTER TABLE simulator_favorite_list_simulator ADD CONSTRAINT FK_3D4DCF2C7FA78211 FOREIGN KEY (simulator_favorite_list_id) REFERENCES simulator_favorite_list (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE simulator_favorite_list_simulator ADD CONSTRAINT FK_3D4DCF2CE4D71D57 FOREIGN KEY (simulator_id) REFERENCES simulator (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE tipico_bet ADD CONSTRAINT FK_5C4B55D85C2DB310 FOREIGN KEY (corresponded_betano_bet_id) REFERENCES betano_bet (id)'
        );
        $this->addSql(
            'ALTER TABLE tipico_both_teams_score_odd ADD CONSTRAINT FK_72F8C680D871DC26 FOREIGN KEY (bet_id) REFERENCES tipico_bet (id)'
        );
        $this->addSql(
            'ALTER TABLE tipico_head_to_head_odd ADD CONSTRAINT FK_B5C4727ED871DC26 FOREIGN KEY (bet_id) REFERENCES tipico_bet (id)'
        );
        $this->addSql(
            'ALTER TABLE tipico_over_under_odd ADD CONSTRAINT FK_589F4ACED871DC26 FOREIGN KEY (bet_id) REFERENCES tipico_bet (id)'
        );
        $this->addSql(
            'ALTER TABLE tipico_placement ADD CONSTRAINT FK_CA84E5D7E4D71D57 FOREIGN KEY (simulator_id) REFERENCES simulator (id)'
        );
        $this->addSql(
            'ALTER TABLE tipico_placement_tipico_bet ADD CONSTRAINT FK_18709411275BA6DF FOREIGN KEY (tipico_placement_id) REFERENCES tipico_placement (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE tipico_placement_tipico_bet ADD CONSTRAINT FK_18709411C31990ED FOREIGN KEY (tipico_bet_id) REFERENCES tipico_bet (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bwin_bet DROP FOREIGN KEY FK_40052BC9C31990ED');
        $this->addSql('ALTER TABLE simulator DROP FOREIGN KEY FK_81C34CA7D5CAD932');
        $this->addSql('ALTER TABLE simulator_tipico_bet DROP FOREIGN KEY FK_7D1B578CE4D71D57');
        $this->addSql('ALTER TABLE simulator_tipico_bet DROP FOREIGN KEY FK_7D1B578CC31990ED');
        $this->addSql('ALTER TABLE simulator_detail_statistic DROP FOREIGN KEY FK_6A31A9DDE4D71D57');
        $this->addSql('ALTER TABLE simulator_favorite_list_simulator DROP FOREIGN KEY FK_3D4DCF2C7FA78211');
        $this->addSql('ALTER TABLE simulator_favorite_list_simulator DROP FOREIGN KEY FK_3D4DCF2CE4D71D57');
        $this->addSql('ALTER TABLE tipico_bet DROP FOREIGN KEY FK_5C4B55D85C2DB310');
        $this->addSql('ALTER TABLE tipico_both_teams_score_odd DROP FOREIGN KEY FK_72F8C680D871DC26');
        $this->addSql('ALTER TABLE tipico_head_to_head_odd DROP FOREIGN KEY FK_B5C4727ED871DC26');
        $this->addSql('ALTER TABLE tipico_over_under_odd DROP FOREIGN KEY FK_589F4ACED871DC26');
        $this->addSql('ALTER TABLE tipico_placement DROP FOREIGN KEY FK_CA84E5D7E4D71D57');
        $this->addSql('ALTER TABLE tipico_placement_tipico_bet DROP FOREIGN KEY FK_18709411275BA6DF');
        $this->addSql('ALTER TABLE tipico_placement_tipico_bet DROP FOREIGN KEY FK_18709411C31990ED');
        $this->addSql('DROP TABLE betano_bet');
        $this->addSql('DROP TABLE betting_provider_backup_file');
        $this->addSql('DROP TABLE bwin_bet');
        $this->addSql('DROP TABLE parameter');
        $this->addSql('DROP TABLE simulation_strategy');
        $this->addSql('DROP TABLE simulator');
        $this->addSql('DROP TABLE simulator_tipico_bet');
        $this->addSql('DROP TABLE simulator_detail_statistic');
        $this->addSql('DROP TABLE simulator_favorite_list');
        $this->addSql('DROP TABLE simulator_favorite_list_simulator');
        $this->addSql('DROP TABLE tipico_bet');
        $this->addSql('DROP TABLE tipico_both_teams_score_odd');
        $this->addSql('DROP TABLE tipico_head_to_head_odd');
        $this->addSql('DROP TABLE tipico_over_under_odd');
        $this->addSql('DROP TABLE tipico_placement');
        $this->addSql('DROP TABLE tipico_placement_tipico_bet');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
