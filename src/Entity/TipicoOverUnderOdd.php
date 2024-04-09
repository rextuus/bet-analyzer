<?php

namespace App\Entity;

use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\TipicoOverUnderOddRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TipicoOverUnderOddRepository::class)]
class TipicoOverUnderOdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tipicoOverUnderOdds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tipico_head_to_head_odd'])]
    private ?TipicoBet $bet = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $overValue = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $underValue = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $targetValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBet(): ?TipicoBet
    {
        return $this->bet;
    }

    public function setBet(?TipicoBet $bet): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getOverValue(): ?float
    {
        return $this->overValue;
    }

    public function setOverValue(float $overValue): static
    {
        $this->overValue = $overValue;

        return $this;
    }

    public function getUnderValue(): ?float
    {
        return $this->underValue;
    }

    public function setUnderValue(float $underValue): static
    {
        $this->underValue = $underValue;

        return $this;
    }

    public function getTargetValue(): ?float
    {
        return $this->targetValue;
    }

    public function setTargetValue(float $targetValue): static
    {
        $this->targetValue = $targetValue;

        return $this;
    }
}
