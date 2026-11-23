<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Neuere symfony/doctrine-messenger-Versionen (6.4.x) erwarten für messenger_messages einen zusammengesetzten
 * Index (queue_name, available_at, delivered_at, id) statt der drei Einzelindizes – passend zu der Abfrage, mit
 * der der Worker die nächste Nachricht holt. Ohne die Anpassung meldet doctrine:schema:validate „nicht synchron“.
 *
 * Erst den neuen Index anlegen, dann die alten entfernen, damit der Worker zwischendurch nicht ohne Index läuft.
 * Die Tabelle ist klein (nur offene Nachrichten), das geht schnell.
 */
final class Version20261123000000 extends AbstractMigration
{
    private const NEW_INDEX = 'IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750';
    private const OLD_INDEXES = [
        'IDX_75EA56E0FB7336F0' => '(queue_name)',
        'IDX_75EA56E0E3BD61CE' => '(available_at)',
        'IDX_75EA56E016BA31DB' => '(delivered_at)',
    ];

    public function getDescription(): string
    {
        return 'messenger_messages: zusammengesetzter Index statt drei Einzelindizes';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('messenger_messages');

        if (!$table->hasIndex(self::NEW_INDEX)) {
            $this->addSql(
                sprintf(
                    'CREATE INDEX %s ON messenger_messages (queue_name, available_at, delivered_at, id)',
                    self::NEW_INDEX
                )
            );
        }
        foreach (array_keys(self::OLD_INDEXES) as $name) {
            if ($table->hasIndex($name)) {
                $this->addSql(sprintf('DROP INDEX %s ON messenger_messages', $name));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('messenger_messages');

        foreach (self::OLD_INDEXES as $name => $columns) {
            if (!$table->hasIndex($name)) {
                $this->addSql(sprintf('CREATE INDEX %s ON messenger_messages %s', $name, $columns));
            }
        }
        if ($table->hasIndex(self::NEW_INDEX)) {
            $this->addSql(sprintf('DROP INDEX %s ON messenger_messages', self::NEW_INDEX));
        }
    }
}
