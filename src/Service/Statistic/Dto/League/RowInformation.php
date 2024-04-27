<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto\League;


class RowInformation
{
    /**
     * @var Row[]
     */
    private array $rows;

    private string $description;
    private float $overall;
    private int $over;
    private int $under;

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): RowInformation
    {
        $this->rows = $rows;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): RowInformation
    {
        $this->description = $description;
        return $this;
    }

    public function getOverall(): float
    {
        return $this->overall;
    }

    public function setOverall(float $overall): RowInformation
    {
        $this->overall = $overall;
        return $this;
    }

    public function getOver(): int
    {
        return $this->over;
    }

    public function setOver(int $over): RowInformation
    {
        $this->over = $over;
        return $this;
    }

    public function getUnder(): int
    {
        return $this->under;
    }

    public function setUnder(int $under): RowInformation
    {
        $this->under = $under;
        return $this;
    }
}
