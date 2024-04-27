<?php

namespace App\Entity\Spm;

use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\Content\PlacedBet\PlacedBetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlacedBetRepository::class)]
class PlacedBet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fixtureApiId = null;

    #[ORM\Column(type: Types::JSON)]
    private array $oddApiIds = [];

    #[ORM\Column]
    private ?float $wager = null;

    #[ORM\Column]
    private ?float $odd = null;

    #[ORM\Column]
    private ?bool $won = null;

    #[ORM\Column]
    private ?float $output = null;

    #[ORM\Column]
    private ?int $matchDay = null;

    #[ORM\Column(type: "string", enumType: BetRowVariant::class)]
    private BetRowVariant $variant;

    #[ORM\ManyToOne(inversedBy: 'placedBets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimpleBetRow $betRow = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFixtureApiId(): ?int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): static
    {
        $this->fixtureApiId = $fixtureApiId;

        return $this;
    }

    public function getOddApiIds(): array
    {
        return $this->oddApiIds;
    }

    public function setOddApiIds(array $oddApiIds): static
    {
        $this->oddApiIds = $oddApiIds;

        return $this;
    }

    public function getWager(): ?float
    {
        return $this->wager;
    }

    public function setWager(float $wager): static
    {
        $this->wager = $wager;

        return $this;
    }

    public function getOdd(): ?float
    {
        return $this->odd;
    }

    public function setOdd(float $odd): static
    {
        $this->odd = $odd;

        return $this;
    }

    public function isWon(): ?bool
    {
        return $this->won;
    }

    public function setWon(bool $won): static
    {
        $this->won = $won;

        return $this;
    }

    public function getOutput(): ?float
    {
        return $this->output;
    }

    public function setOutput(float $output): static
    {
        $this->output = $output;

        return $this;
    }

    public function getMatchDay(): ?int
    {
        return $this->matchDay;
    }

    public function setMatchDay(int $matchDay): static
    {
        $this->matchDay = $matchDay;

        return $this;
    }

    public function getVariant(): BetRowVariant
    {
        return $this->variant;
    }

    public function setVariant(BetRowVariant $variant): PlacedBet
    {
        $this->variant = $variant;
        return $this;
    }

    public function getBetRow(): ?SimpleBetRow
    {
        return $this->betRow;
    }

    public function setBetRow(?SimpleBetRow $betRow): static
    {
        $this->betRow = $betRow;

        return $this;
    }
}
