<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto;

use App\Entity\BetRowOddFilter;
use App\Entity\SimpleBetRow;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowStatistics
{
    private SimpleBetRow $highest;

    /**
     * @var BetRowOddFilter[]
     */
    private array $missingHomeFilters;

    /**
     * @var BetRowOddFilter[]
     */
    private array $missingDrawFilters;

    /**
     * @var BetRowOddFilter[]
     */
    private array $missingAwayFilters;
}
