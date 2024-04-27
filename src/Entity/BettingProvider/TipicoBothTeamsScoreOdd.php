<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\TipicoBothTeamsScoreOddRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TipicoBothTeamsScoreOddRepository::class)]
class TipicoBothTeamsScoreOdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'tipicoBothTeamsScoreBet', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TipicoBet $bet = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $conditionTrueValue = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $conditionFalseValue = null;

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

    public function getConditionTrueValue(): ?float
    {
        return $this->conditionTrueValue;
    }

    public function setConditionTrueValue(float $conditionTrueValue): static
    {
        $this->conditionTrueValue = $conditionTrueValue;

        return $this;
    }

    public function getConditionFalseValue(): ?float
    {
        return $this->conditionFalseValue;
    }

    public function setConditionFalseValue(float $conditionFalseValue): static
    {
        $this->conditionFalseValue = $conditionFalseValue;

        return $this;
    }
}
