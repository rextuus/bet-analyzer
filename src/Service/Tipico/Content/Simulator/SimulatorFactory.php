<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\BettingProvider\Simulator;
use App\Service\Tipico\Content\Simulator\Data\SimulatorData;


class SimulatorFactory
{
    public function createByData(SimulatorData $data): Simulator
    {
        $simulator = $this->createNewInstance();
        $this->mapData($data, $simulator);
        return $simulator;
    }

    public function mapData(SimulatorData $data, Simulator $simulator): Simulator
{
    $simulator->setIdentifier($data->getIdentifier());
    $simulator->setCashBox($data->getCashBox());
    $simulator->setStrategy($data->getStrategy());
    $simulator->setCurrentIn($data->getCurrentIn());

    foreach ($data->getFixtures() as $fixture){
        $simulator->addFixture($fixture);
    }

    foreach ($data->getPlacements() as $placement){
        $simulator->addTipicoPlacement($placement);
    }

    return $simulator;
}

    private function createNewInstance(): Simulator
    {
        return new Simulator();
    }
}
