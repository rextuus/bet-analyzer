<?php

namespace App\Entity\BettingProvider;
use App\Service\Tipico\Content\SimulationStrategy\AdditionalProcessingIdent;
use App\Service\Tipico\Content\SimulationStrategy\SimulationStrategyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimulationStrategyRepository::class)]
class SimulationStrategy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'json', nullable: false)]
    private mixed $parameters;

    #[ORM\Column(type: "string", nullable: true, enumType: AdditionalProcessingIdent::class)]
    private ?AdditionalProcessingIdent $additionalProcessingIdent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getParameters(): mixed
    {
        return $this->parameters;
    }

    public function setParameters(mixed $parameters): SimulationStrategy
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getAdditionalProcessingIdent(): ?AdditionalProcessingIdent
    {
        return $this->additionalProcessingIdent;
    }

    public function setAdditionalProcessingIdent(?AdditionalProcessingIdent $additionalProcessingIdent): SimulationStrategy
    {
        $this->additionalProcessingIdent = $additionalProcessingIdent;
        return $this;
    }
}
