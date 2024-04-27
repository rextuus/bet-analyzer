<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\League\SpmLeagueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmLeagueRepository::class)]
class SpmLeague
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $country = null;

    #[ORM\Column(length: 255)]
    private ?string $short = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(unique: true)]
    private ?int $apiId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function setCountry(int $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): static
    {
        $this->short = $short;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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
}
