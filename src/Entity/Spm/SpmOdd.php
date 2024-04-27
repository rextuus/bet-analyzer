<?php

namespace App\Entity\Spm;

use App\Service\Sportmonks\Content\Odd\SpmOddRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpmOddRepository::class)]
class SpmOdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'bigint', unique: true)]
    private ?int $apiId = null;

    #[ORM\Column]
    private ?int $fixtureApiId = null;

    #[ORM\Column]
    private ?int $bookmakerApiId = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column(length: 255)]
    private ?string $marketDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

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

    public function getFixtureApiId(): ?int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): static
    {
        $this->fixtureApiId = $fixtureApiId;

        return $this;
    }

    public function getBookmakerApiId(): ?int
    {
        return $this->bookmakerApiId;
    }

    public function setBookmakerApiId(int $bookmakerApiId): static
    {
        $this->bookmakerApiId = $bookmakerApiId;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getMarketDescription(): ?string
    {
        return $this->marketDescription;
    }

    public function setMarketDescription(string $marketDescription): static
    {
        $this->marketDescription = $marketDescription;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
