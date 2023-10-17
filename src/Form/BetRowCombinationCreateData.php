<?php
declare(strict_types=1);

namespace App\Form;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCombinationCreateData
{
    private string $ident;
    private bool $active;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): BetRowCombinationCreateData
    {
        $this->active = $active;
        return $this;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): BetRowCombinationCreateData
    {
        $this->ident = $ident;
        return $this;
    }


}
