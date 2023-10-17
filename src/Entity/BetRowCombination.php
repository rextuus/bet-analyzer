<?php

namespace App\Entity;

use App\Service\Statistic\Content\BetRowCombination\BetRowCombinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetRowCombinationRepository::class)]
class BetRowCombination
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: SimpleBetRow::class, inversedBy: 'betRowCombinations')]
    private Collection $betRows;

    #[ORM\Column(length: 255)]
    private ?string $ident = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?bool $evaluated = null;

    public function __construct()
    {
        $this->betRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SimpleBetRow>
     */
    public function getBetRows(): Collection
    {
        return $this->betRows;
    }

    public function addBetRow(SimpleBetRow $betRow): static
    {
        if (!$this->betRows->contains($betRow)) {
            $this->betRows->add($betRow);
        }

        return $this;
    }

    public function removeBetRow(SimpleBetRow $betRow): static
    {
        $this->betRows->removeElement($betRow);

        return $this;
    }

    public function getIdent(): ?string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): static
    {
        $this->ident = $ident;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function isEvaluated(): ?bool
    {
        return $this->evaluated;
    }

    public function setEvaluated(bool $evaluated): static
    {
        $this->evaluated = $evaluated;

        return $this;
    }
}
