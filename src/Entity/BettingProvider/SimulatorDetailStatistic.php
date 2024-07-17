<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\SimulatorDetailStatistic\SimulatorDetailStatisticRepository;
use DateTimeInterface;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $edited = null;

    #[ORM\OneToOne(inversedBy: 'simulatorDetailStatistic', cascade: ['persist', 'remove'])]
    private ?Simulator $simulator = null;

    #[ORM\Column]
    private ?float $mondayTotal = null;

    #[ORM\Column]
    private ?float $mondayAverage = null;

    #[ORM\Column]
    private ?float $tuesdayTotal = null;

    #[ORM\Column]
    private ?float $tuesdayAverage = null;

    #[ORM\Column]
    private ?float $wednesdayTotal = null;

    #[ORM\Column]
    private ?float $wednesdayAverage = null;

    #[ORM\Column]
    private ?float $thursdayTotal = null;

    #[ORM\Column]
    private ?float $thursdayAverage = null;

    #[ORM\Column]
    private ?float $fridayTotal = null;

    #[ORM\Column]
    private ?float $fridayAverage = null;

    #[ORM\Column]
    private ?float $saturdayTotal = null;

    #[ORM\Column]
    private ?float $saturdayAverage = null;

    #[ORM\Column]
    private ?float $sundayTotal = null;

    #[ORM\Column]
    private ?float $sundayAverage = null;

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

    public function getEdited(): ?DateTimeInterface
    {
        return $this->edited;
    }

    public function setEdited(?DateTimeInterface $edited): SimulatorDetailStatistic
    {
        $this->edited = $edited;
        return $this;
    }

    public function getMondayTotal(): ?float
    {
        return $this->mondayTotal;
    }

    public function setMondayTotal(float $mondayTotal): static
    {
        $this->mondayTotal = $mondayTotal;

        return $this;
    }

    public function getMondayAverage(): ?float
    {
        return $this->mondayAverage;
    }

    public function setMondayAverage(float $mondayAverage): static
    {
        $this->mondayAverage = $mondayAverage;

        return $this;
    }

    public function getTuesdayTotal(): ?float
    {
        return $this->tuesdayTotal;
    }

    public function setTuesdayTotal(?float $tuesdayTotal): SimulatorDetailStatistic
    {
        $this->tuesdayTotal = $tuesdayTotal;
        return $this;
    }

    public function getTuesdayAverage(): ?float
    {
        return $this->tuesdayAverage;
    }

    public function setTuesdayAverage(?float $tuesdayAverage): SimulatorDetailStatistic
    {
        $this->tuesdayAverage = $tuesdayAverage;
        return $this;
    }

    public function getWednesdayTotal(): ?float
    {
        return $this->wednesdayTotal;
    }

    public function setWednesdayTotal(?float $wednesdayTotal): SimulatorDetailStatistic
    {
        $this->wednesdayTotal = $wednesdayTotal;
        return $this;
    }

    public function getWednesdayAverage(): ?float
    {
        return $this->wednesdayAverage;
    }

    public function setWednesdayAverage(?float $wednesdayAverage): SimulatorDetailStatistic
    {
        $this->wednesdayAverage = $wednesdayAverage;
        return $this;
    }

    public function getThursdayTotal(): ?float
    {
        return $this->thursdayTotal;
    }

    public function setThursdayTotal(?float $thursdayTotal): SimulatorDetailStatistic
    {
        $this->thursdayTotal = $thursdayTotal;
        return $this;
    }

    public function getThursdayAverage(): ?float
    {
        return $this->thursdayAverage;
    }

    public function setThursdayAverage(?float $thursdayAverage): SimulatorDetailStatistic
    {
        $this->thursdayAverage = $thursdayAverage;
        return $this;
    }

    public function getFridayTotal(): ?float
    {
        return $this->fridayTotal;
    }

    public function setFridayTotal(?float $fridayTotal): SimulatorDetailStatistic
    {
        $this->fridayTotal = $fridayTotal;
        return $this;
    }

    public function getFridayAverage(): ?float
    {
        return $this->fridayAverage;
    }

    public function setFridayAverage(?float $fridayAverage): SimulatorDetailStatistic
    {
        $this->fridayAverage = $fridayAverage;
        return $this;
    }

    public function getSaturdayTotal(): ?float
    {
        return $this->saturdayTotal;
    }

    public function setSaturdayTotal(?float $saturdayTotal): SimulatorDetailStatistic
    {
        $this->saturdayTotal = $saturdayTotal;
        return $this;
    }

    public function getSaturdayAverage(): ?float
    {
        return $this->saturdayAverage;
    }

    public function setSaturdayAverage(?float $saturdayAverage): SimulatorDetailStatistic
    {
        $this->saturdayAverage = $saturdayAverage;
        return $this;
    }

    public function getSundayTotal(): ?float
    {
        return $this->sundayTotal;
    }

    public function setSundayTotal(?float $sundayTotal): SimulatorDetailStatistic
    {
        $this->sundayTotal = $sundayTotal;
        return $this;
    }

    public function getSundayAverage(): ?float
    {
        return $this->sundayAverage;
    }

    public function setSundayAverage(?float $sundayAverage): SimulatorDetailStatistic
    {
        $this->sundayAverage = $sundayAverage;
        return $this;
    }
}
