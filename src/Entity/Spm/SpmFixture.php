<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\Fixture\SpmFixtureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmFixtureRepository::class)]
class SpmFixture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    #[ORM\Column]
    private ?int $leagueApiId = null;

    #[ORM\Column]
    private ?int $seasonApiId = null;

    #[ORM\Column]
    private ?int $roundApiId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startingAt = null;

    #[ORM\Column]
    private ?int $startingAtTimestamp = null;

    #[ORM\Column(length: 255)]
    private ?string $resultInfo = null;

    #[ORM\Column]
    private bool $oddDecorated = false;

    #[ORM\Column]
    private bool $scoreDecorated = false;

    #[ORM\ManyToMany(targetEntity: OddOutcome::class, mappedBy: 'fixtures')]
    private Collection $oddOutcomes;

    public function __construct()
    {
        $this->oddOutcomes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiId(): ?int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): static
    {
        $this->apiId = $apiId;

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

    public function getRoundApiId(): ?int
    {
        return $this->roundApiId;
    }

    public function setRoundApiId(int $roundApiId): static
    {
        $this->roundApiId = $roundApiId;

        return $this;
    }

    public function getStartingAt(): ?\DateTimeInterface
    {
        return $this->startingAt;
    }

    public function setStartingAt(\DateTimeInterface $startingAt): static
    {
        $this->startingAt = $startingAt;

        return $this;
    }

    public function getStartingAtTimestamp(): ?int
    {
        return $this->startingAtTimestamp;
    }

    public function setStartingAtTimestamp(int $startingAtTimestamp): static
    {
        $this->startingAtTimestamp = $startingAtTimestamp;

        return $this;
    }

    public function getResultInfo(): ?string
    {
        return $this->resultInfo;
    }

    public function setResultInfo(string $resultInfo): static
    {
        $this->resultInfo = $resultInfo;

        return $this;
    }

    public function isOddDecorated(): ?bool
    {
        return $this->oddDecorated;
    }

    public function setOddDecorated(bool $oddDecorated): static
    {
        $this->oddDecorated = $oddDecorated;

        return $this;
    }

    public function isScoreDecorated(): ?bool
    {
        return $this->scoreDecorated;
    }

    public function setScoreDecorated(bool $scoreDecorated): static
    {
        $this->scoreDecorated = $scoreDecorated;

        return $this;
    }

    /**
     * @return Collection<int, OddOutcome>
     */
    public function getOddOutcomes(): Collection
    {
        return $this->oddOutcomes;
    }

    public function addOddOutcome(OddOutcome $oddOutcome): static
    {
        if (!$this->oddOutcomes->contains($oddOutcome)) {
            $this->oddOutcomes->add($oddOutcome);
            $oddOutcome->addFixture($this);
        }

        return $this;
    }

    public function removeOddOutcome(OddOutcome $oddOutcome): static
    {
        if ($this->oddOutcomes->removeElement($oddOutcome)) {
            $oddOutcome->removeFixture($this);
        }

        return $this;
    }
}
