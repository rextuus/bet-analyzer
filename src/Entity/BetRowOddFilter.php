<?php

namespace App\Entity;

use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterRepository;
use App\Service\Evaluation\OddVariant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetRowOddFilterRepository::class)]
class BetRowOddFilter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $min = null;

    #[ORM\Column]
    private ?float $max = null;

    #[ORM\Column(type: "string", enumType: OddVariant::class)]
    private OddVariant $oddVariant;

    #[ORM\Column(type: "string", enumType: BetOn::class)]
    private BetOn $betOn;

    #[ORM\ManyToMany(targetEntity: SimpleBetRow::class, mappedBy: 'betRowFilters')]
    private Collection $simpleBetRows;

    public function __construct()
    {
        $this->simpleBetRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getOddVariant(): OddVariant
    {
        return $this->oddVariant;
    }

    public function setOddVariant(OddVariant $oddVariant): BetRowOddFilter
    {
        $this->oddVariant = $oddVariant;
        return $this;
    }

    public function getBetOn(): BetOn
    {
        return $this->betOn;
    }

    public function setBetOn(BetOn $betOn): BetRowOddFilter
    {
        $this->betOn = $betOn;
        return $this;
    }

    /**
     * @return Collection<int, SimpleBetRow>
     */
    public function getSimpleBetRows(): Collection
    {
        return $this->simpleBetRows;
    }

    public function addSimpleBetRow(SimpleBetRow $simpleBetRow): static
    {
        if (!$this->simpleBetRows->contains($simpleBetRow)) {
            $this->simpleBetRows->add($simpleBetRow);
            $simpleBetRow->addBetRowFilter($this);
        }

        return $this;
    }

    public function removeSimpleBetRow(SimpleBetRow $simpleBetRow): static
    {
        if ($this->simpleBetRows->removeElement($simpleBetRow)) {
            $simpleBetRow->removeBetRowFilter($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
          '%s -> [%.2f-%.2f]',
            $this->betOn->name,
            $this->min,
            $this->max
        );
    }
}
