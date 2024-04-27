<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;


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
