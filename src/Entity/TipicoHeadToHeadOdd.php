<?php

namespace App\Entity;

use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\TipicoHeadToHeadOddRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipicoHeadToHeadOddRepository::class)]
class TipicoHeadToHeadOdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'tipicoHeadToHeadScore', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TipicoBet $bet = null;

    #[ORM\Column]
    private ?float $homeTeamValue = null;

    #[ORM\Column]
    private ?float $awayTeamValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBet(): ?TipicoBet
    {
        return $this->bet;
    }

    public function setBet(TipicoBet $bet): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getHomeTeamValue(): ?float
    {
        return $this->homeTeamValue;
    }

    public function setHomeTeamValue(float $homeTeamValue): static
    {
        $this->homeTeamValue = $homeTeamValue;

        return $this;
    }

    public function getAwayTeamValue(): ?float
    {
        return $this->awayTeamValue;
    }

    public function setAwayTeamValue(float $awayTeamValue): static
    {
        $this->awayTeamValue = $awayTeamValue;

        return $this;
    }
}
