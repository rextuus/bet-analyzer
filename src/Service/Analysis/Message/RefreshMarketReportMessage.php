<?php

declare(strict_types=1);

namespace App\Service\Analysis\Message;

/**
 * Marktanalyse im Worker neu berechnen (Knopf auf /analysis). Dauert einige Sekunden, darum nicht im Request.
 */
final class RefreshMarketReportMessage
{
}
