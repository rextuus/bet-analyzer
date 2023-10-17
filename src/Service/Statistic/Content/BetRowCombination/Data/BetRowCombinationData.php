<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowCombination\Data;

use App\Entity\BetRowCombination;
use App\Entity\SimpleBetRow;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCombinationData
{
    /**
     * @var SimpleBetRow[]
     */
    private array $rows;

    private string $ident;

    private bool $active;
    private bool $evaluated;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): BetRowCombinationData
    {
        $this->active = $active;
        return $this;
    }

    public function isEvaluated(): bool
    {
        return $this->evaluated;
    }

    public function setEvaluated(bool $evaluated): BetRowCombinationData
    {
        $this->evaluated = $evaluated;
        return $this;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): BetRowCombinationData
    {
        $this->ident = $ident;
        return $this;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): BetRowCombinationData
    {
        $this->rows = $rows;
        return $this;
    }

    public function initFromEntity(BetRowCombination $betRowCombination): BetRowCombinationData
    {
        $this->setActive($betRowCombination->isActive());
        $this->setEvaluated($betRowCombination->isEvaluated());
        $this->setRows($betRowCombination->getBetRows()->toArray());
        $this->setIdent($betRowCombination->getIdent());
        return $this;
    }
}
