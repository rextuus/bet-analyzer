<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\TipicoHeadToHeadOddRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TipicoHeadToHeadOddRepository::class)]
class TipicoHeadToHeadOdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'tipicoHeadToHeadScore', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TipicoBet $bet = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $homeTeamValue = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
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
