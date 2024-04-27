<?php

namespace App\Entity\BettingProvider;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\TipicoBet\TipicoBetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TipicoBetRepository::class)]
class TipicoBet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $tipicoId = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $tipicoHomeTeamId = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $tipicoAwayTeamId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tipico_bet'])]
    private ?string $homeTeamName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tipico_bet'])]
    private ?string $awayTeamName = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['tipico_bet'])]
    private ?int $startAtTimeStamp = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $oddHome = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $oddDraw = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?float $oddAway = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $endScoreHome = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?int $endScoreAway = null;

    #[ORM\Column]
    #[Groups(['tipico_bet'])]
    private ?bool $finished = null;

    #[ORM\Column(type: "string", enumType: BetOn::class)]
    #[Groups(['tipico_bet'])]
    private BetOn $result;

    #[ORM\ManyToMany(targetEntity: Simulator::class, mappedBy: 'fixtures')]
    private Collection $simulators;

    #[ORM\ManyToMany(targetEntity: TipicoPlacement::class, mappedBy: 'fixtures', fetch: 'EXTRA_LAZY')]
    private Collection $tipicoPlacements;

    #[ORM\OneToMany(mappedBy: 'bet', targetEntity: TipicoOverUnderOdd::class)]
    #[Groups(['tipico_bet'])]
    private Collection $tipicoOverUnderOdds;

    #[ORM\OneToOne(mappedBy: 'bet', cascade: ['persist', 'remove'])]
    #[Groups(['tipico_bet'])]
    private ?TipicoBothTeamsScoreOdd $tipicoBothTeamsScoreBet = null;

    #[ORM\OneToOne(mappedBy: 'bet', cascade: ['persist', 'remove'])]
    #[Groups(['tipico_bet'])]
    private ?TipicoHeadToHeadOdd $tipicoHeadToHeadScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $sportRadarId = null;

    #[ORM\OneToOne(inversedBy: 'tipicoBet', cascade: ['persist', 'remove'])]
    private ?BetanoBet $correspondedBetanoBet = null;

    #[ORM\OneToOne(mappedBy: 'tipicoBet', cascade: ['persist', 'remove'])]
    private ?BwinBet $bwinBet = null;

    public function __construct()
    {
        $this->simulators = new ArrayCollection();
        $this->tipicoPlacements = new ArrayCollection();
        $this->tipicoOverUnderOdds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipicoId(): ?int
    {
        return $this->tipicoId;
    }

    public function setTipicoId(int $tipicoId): static
    {
        $this->tipicoId = $tipicoId;

        return $this;
    }

    public function getTipicoHomeTeamId(): ?int
    {
        return $this->tipicoHomeTeamId;
    }

    public function setTipicoHomeTeamId(int $tipicoHomeTeamId): static
    {
        $this->tipicoHomeTeamId = $tipicoHomeTeamId;

        return $this;
    }

    public function getTipicoAwayTeamId(): ?int
    {
        return $this->tipicoAwayTeamId;
    }

    public function setTipicoAwayTeamId(int $tipicoAwayTeamId): static
    {
        $this->tipicoAwayTeamId = $tipicoAwayTeamId;

        return $this;
    }

    public function getHomeTeamName(): ?string
    {
        return $this->homeTeamName;
    }

    public function setHomeTeamName(string $homeTeamName): static
    {
        $this->homeTeamName = $homeTeamName;

        return $this;
    }

    public function getAwayTeamName(): ?string
    {
        return $this->awayTeamName;
    }

    public function setAwayTeamName(string $awayTeamName): static
    {
        $this->awayTeamName = $awayTeamName;

        return $this;
    }

    public function getStartAtTimeStamp(): ?int
    {
        return $this->startAtTimeStamp;
    }

    public function setStartAtTimeStamp(int $startAtTimeStamp): static
    {
        $this->startAtTimeStamp = $startAtTimeStamp;

        return $this;
    }

    public function getOddHome(): ?float
    {
        return $this->oddHome;
    }

    public function setOddHome(float $oddHome): static
    {
        $this->oddHome = $oddHome;

        return $this;
    }

    public function getOddDraw(): ?float
    {
        return $this->oddDraw;
    }

    public function setOddDraw(float $oddDraw): static
    {
        $this->oddDraw = $oddDraw;

        return $this;
    }

    public function getOddAway(): ?float
    {
        return $this->oddAway;
    }

    public function setOddAway(float $oddAway): static
    {
        $this->oddAway = $oddAway;

        return $this;
    }

    public function getEndScoreHome(): ?int
    {
        return $this->endScoreHome;
    }

    public function setEndScoreHome(int $endScoreHome): static
    {
        $this->endScoreHome = $endScoreHome;

        return $this;
    }

    public function getEndScoreAway(): ?int
    {
        return $this->endScoreAway;
    }

    public function setEndScoreAway(int $endScoreAway): static
    {
        $this->endScoreAway = $endScoreAway;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): static
    {
        $this->finished = $finished;

        return $this;
    }

    public function getResult(): BetOn
    {
        return $this->result;
    }

    public function setResult(BetOn $result): TipicoBet
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return Collection<int, Simulator>
     */
    public function getSimulators(): Collection
    {
        return $this->simulators;
    }

    public function addSimulator(Simulator $simulator): static
    {
        if (!$this->simulators->contains($simulator)) {
            $this->simulators->add($simulator);
            $simulator->addFixture($this);
        }

        return $this;
    }

    public function removeSimulator(Simulator $simulator): static
    {
        if ($this->simulators->removeElement($simulator)) {
            $simulator->removeFixture($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TipicoPlacement>
     */
    public function getTipicoPlacements(): Collection
    {
        return $this->tipicoPlacements;
    }

    public function addTipicoPlacement(TipicoPlacement $tipicoPlacement): static
    {
        if (!$this->tipicoPlacements->contains($tipicoPlacement)) {
            $this->tipicoPlacements->add($tipicoPlacement);
            $tipicoPlacement->addFixture($this);
        }

        return $this;
    }

    public function removeTipicoPlacement(TipicoPlacement $tipicoPlacement): static
    {
        if ($this->tipicoPlacements->removeElement($tipicoPlacement)) {
            $tipicoPlacement->removeFixture($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TipicoOverUnderOdd>
     */
    public function getTipicoOverUnderOdds(): Collection
    {
        return $this->tipicoOverUnderOdds;
    }

    public function addTipicoOverUnderOdd(TipicoOverUnderOdd $tipicoOverUnderOdd): static
    {
        if (!$this->tipicoOverUnderOdds->contains($tipicoOverUnderOdd)) {
            $this->tipicoOverUnderOdds->add($tipicoOverUnderOdd);
            $tipicoOverUnderOdd->setBet($this);
        }

        return $this;
    }

    public function removeTipicoOverUnderOdd(TipicoOverUnderOdd $tipicoOverUnderOdd): static
    {
        if ($this->tipicoOverUnderOdds->removeElement($tipicoOverUnderOdd)) {
            // set the owning side to null (unless already changed)
            if ($tipicoOverUnderOdd->getBet() === $this) {
                $tipicoOverUnderOdd->setBet(null);
            }
        }

        return $this;
    }

    public function getTipicoBothTeamsScoreBet(): ?TipicoBothTeamsScoreOdd
    {
        return $this->tipicoBothTeamsScoreBet;
    }

    public function setTipicoBothTeamsScoreBet(TipicoBothTeamsScoreOdd $tipicoBothTeamsScoreBet): static
    {
        // set the owning side of the relation if necessary
        if ($tipicoBothTeamsScoreBet->getBet() !== $this) {
            $tipicoBothTeamsScoreBet->setBet($this);
        }

        $this->tipicoBothTeamsScoreBet = $tipicoBothTeamsScoreBet;

        return $this;
    }

    public function getTipicoHeadToHeadScore(): ?TipicoHeadToHeadOdd
    {
        return $this->tipicoHeadToHeadScore;
    }

    public function setTipicoHeadToHeadScore(TipicoHeadToHeadOdd $tipicoHeadToHeadScore): static
    {
        // set the owning side of the relation if necessary
        if ($tipicoHeadToHeadScore->getBet() !== $this) {
            $tipicoHeadToHeadScore->setBet($this);
        }

        $this->tipicoHeadToHeadScore = $tipicoHeadToHeadScore;

        return $this;
    }

    public function getSportRadarId(): ?int
    {
        return $this->sportRadarId;
    }

    public function setSportRadarId(?int $sportRadarId): static
    {
        $this->sportRadarId = $sportRadarId;

        return $this;
    }

    public function getCorrespondedBetanoBet(): ?BetanoBet
    {
        return $this->correspondedBetanoBet;
    }

    public function setCorrespondedBetanoBet(?BetanoBet $correspondedBetanoBet): static
    {
        $this->correspondedBetanoBet = $correspondedBetanoBet;

        return $this;
    }

    public function getBwinBet(): ?BwinBet
    {
        return $this->bwinBet;
    }

    public function setBwinBet(BwinBet $bwinBet): static
    {
        // set the owning side of the relation if necessary
        if ($bwinBet->getTipicoBet() !== $this) {
            $bwinBet->setTipicoBet($this);
        }

        $this->bwinBet = $bwinBet;

        return $this;
    }
}
