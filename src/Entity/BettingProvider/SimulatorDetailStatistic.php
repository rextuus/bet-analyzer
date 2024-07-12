<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\SimulatorDetailStatistic\SimulatorDetailStatisticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimulatorDetailStatisticRepository::class)]
class SimulatorDetailStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\OneToOne(inversedBy: 'simulatorDetailStatistic', cascade: ['persist', 'remove'])]
    private ?Simulator $simulator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSimulator(): ?Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(?Simulator $simulator): static
    {
        $this->simulator = $simulator;

        return $this;
    }
}
