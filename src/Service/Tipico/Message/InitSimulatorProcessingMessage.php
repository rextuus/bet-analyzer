<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class InitSimulatorProcessingMessage
{
    private SimulatorProcessBulk $bulk;

    public function __construct(SimulatorProcessBulk $bulk)
    {
        $this->bulk = $bulk;
    }

    public function getBulk(): SimulatorProcessBulk
    {
        return $this->bulk;
    }
}
