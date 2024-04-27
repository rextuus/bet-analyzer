<?php

namespace App\Entity\BettingProvider;

use App\Service\BettingProvider\Bwin\Content\BwinBet\BwinBetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BwinBetRepository::class)]
class BwinBet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $bwinId = null;

    #[ORM\Column(length: 255)]
    private ?string $homeTeamName = null;

    #[ORM\Column(length: 255)]
    private ?string $awayTeamName = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $startAtTimeStamp = null;

    #[ORM\Column]
    private ?float $oddHome = null;

    #[ORM\Column]
    private ?float $oddDraw = null;

    #[ORM\Column]
    private ?float $oddAway = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?int $sportRadarId = null;

    #[ORM\OneToOne(inversedBy: 'bwinBet', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TipicoBet $tipicoBet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBwinId(): ?string
    {
        return $this->bwinId;
    }

    public function setBwinId(string $bwinId): static
    {
        $this->bwinId = $bwinId;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getSportRadarId(): ?int
    {
        return $this->sportRadarId;
    }

    public function setSportRadarId(int $sportRadarId): static
    {
        $this->sportRadarId = $sportRadarId;

        return $this;
    }

    public function getTipicoBet(): ?TipicoBet
    {
        return $this->tipicoBet;
    }

    public function setTipicoBet(TipicoBet $tipicoBet): static
    {
        $this->tipicoBet = $tipicoBet;

        return $this;
    }
}
