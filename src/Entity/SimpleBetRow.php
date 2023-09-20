<?php

namespace App\Entity;

use App\Service\Evaluation\Content\BetRow\BetRowInterface;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowRepository;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimpleBetRowRepository::class)]
class SimpleBetRow implements BetRowInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'betRow', targetEntity: PlacedBet::class)]
    private Collection $placedBets;

    #[ORM\Column]
    private ?int $leagueApiId = null;

    #[ORM\Column]
    private ?int $seasonApiId = null;

    #[ORM\Column(type: "string", enumType: BetRowVariant::class)]
    private BetRowVariant $variant;

    #[ORM\Column(type: "string", enumType: OddAccumulationVariant::class)]
    private OddAccumulationVariant $accumulationVariant;

    #[ORM\Column]
    private ?bool $includeTax = true;

    #[ORM\Column]
    private ?float $cashBox = null;

    #[ORM\Column]
    private ?float $wager = null;

    #[ORM\ManyToMany(targetEntity: BetRowOddFilter::class, inversedBy: 'simpleBetRows')]
    private Collection $betRowFilters;

    public function __construct()
    {
        $this->placedBets = new ArrayCollection();
        $this->betRowFilters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, PlacedBet>
     */
    public function getPlacedBets(): Collection
    {
        return $this->placedBets;
    }

    public function addPlacedBet(PlacedBet $placedBet): static
    {
        if (!$this->placedBets->contains($placedBet)) {
            $this->placedBets->add($placedBet);
            $placedBet->setBetRow($this);
        }

        return $this;
    }

    public function removePlacedBet(PlacedBet $placedBet): static
    {
        if ($this->placedBets->removeElement($placedBet)) {
            // set the owning side to null (unless already changed)
            if ($placedBet->getBetRow() === $this) {
                $placedBet->setBetRow(null);
            }
        }

        return $this;
    }

    public function getLeagueApiId(): ?int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): static
    {
        $this->leagueApiId = $leagueApiId;

        return $this;
    }

    public function getSeasonApiId(): ?int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): static
    {
        $this->seasonApiId = $seasonApiId;

        return $this;
    }

    public function getVariant(): BetRowVariant
    {
        return $this->variant;
    }

    public function setVariant(BetRowVariant $variant): SimpleBetRow
    {
        $this->variant = $variant;
        return $this;
    }

    public function getCashBox(): ?float
    {
        return $this->cashBox;
    }

    public function setCashBox(float $cashBox): static
    {
        $this->cashBox = $cashBox;

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

    /**
     * @return Collection<int, BetRowOddFilter>
     */
    public function getBetRowOddFilters(): Collection
    {
        return $this->betRowOddFilters;
    }

    public function addBetRowOddFilter(BetRowOddFilter $betRowOddFilter): BetRowInterface
    {
        if (!$this->betRowOddFilters->contains($betRowOddFilter)) {
            $this->betRowOddFilters->add($betRowOddFilter);
            $betRowOddFilter->setBetRow($this);
        }

        return $this;
    }

    public function removeBetRowOddFilter(BetRowOddFilter $betRowOddFilter): static
    {
        if ($this->betRowOddFilters->removeElement($betRowOddFilter)) {
            // set the owning side to null (unless already changed)
            if ($betRowOddFilter->getBetRow() === $this) {
                $betRowOddFilter->setBetRow(null);
            }
        }

        return $this;
    }

    public function isIncludeTax(): ?bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): static
    {
        $this->includeTax = $includeTax;

        return $this;
    }

    public function getAccumulationVariant(): ?OddAccumulationVariant
    {
        return $this->accumulationVariant;
    }

    public function setAccumulationVariant(OddAccumulationVariant $accumulationVariant): static
    {
        $this->accumulationVariant = $accumulationVariant;

        return $this;
    }

    /**
     * @return Collection<int, BetRowOddFilter>
     */
    public function getBetRowFilters(): Collection
    {
        return $this->betRowFilters;
    }

    public function addBetRowFilter(BetRowOddFilter $betRowFilter): static
    {
        if (!$this->betRowFilters->contains($betRowFilter)) {
            $this->betRowFilters->add($betRowFilter);
        }

        return $this;
    }

    public function removeBetRowFilter(BetRowOddFilter $betRowFilter): static
    {
        $this->betRowFilters->removeElement($betRowFilter);

        return $this;
    }
}
