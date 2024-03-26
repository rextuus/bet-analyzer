<?php

namespace App\Entity;

use App\Service\Betano\Content\BetanoBet\BetanoBetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetanoBetRepository::class)]
class BetanoBet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $betanoId = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBetanoId(): ?int
    {
        return $this->betanoId;
    }

    public function setBetanoId(int $betanoId): static
    {
        $this->betanoId = $betanoId;

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
}
