<?php

declare(strict_types=1);

namespace App\Entity\BettingProvider;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Zwischengespeichertes Ergebnis einer aufwendigen Auswertung (z. B. der Marktanalyse über alle Wetten und
 * Platzierungen). Die Berechnung dauert zu lange für einen Seitenaufruf; sie läuft nachts per Cron oder auf Knopfdruck
 * im Worker, die Seite liest nur den letzten Stand.
 */
#[ORM\Entity]
#[ORM\Table(name: 'analysis_snapshot')]
#[ORM\UniqueConstraint(name: 'uniq_analysis_snapshot_name', columns: ['name'])]
class AnalysisSnapshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $name;

    /** @var array<string, mixed> */
    #[ORM\Column(type: Types::JSON)]
    private array $data = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $computedAt;

    #[ORM\Column]
    private int $durationMs = 0;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->computedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    /** @param array<string, mixed> $data */
    public function update(array $data, DateTimeInterface $computedAt, int $durationMs): void
    {
        $this->data = $data;
        $this->computedAt = $computedAt;
        $this->durationMs = $durationMs;
    }

    public function getComputedAt(): DateTimeInterface
    {
        return $this->computedAt;
    }

    public function getDurationMs(): int
    {
        return $this->durationMs;
    }
}
