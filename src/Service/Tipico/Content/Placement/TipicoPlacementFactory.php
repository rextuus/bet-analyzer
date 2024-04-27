<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement;

use App\Entity\BettingProvider\TipicoPlacement;
use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;


class TipicoPlacementFactory
{
    public function createByData(TipicoPlacementData $data): TipicoPlacement
    {
        $tipicoPlacement = $this->createNewInstance();
        $this->mapData($data, $tipicoPlacement);
        return $tipicoPlacement;
    }

    public function mapData(TipicoPlacementData $data, TipicoPlacement $tipicoPlacement): TipicoPlacement
    {
        $tipicoPlacement->setValue($data->getValue());
        $tipicoPlacement->setCreated($data->getCreated());
        $tipicoPlacement->setSimulator($data->getSimulator());
        $tipicoPlacement->setWon($data->isWon());
        $tipicoPlacement->setInput($data->getInput());

        foreach ($data->getFixtures() as $fixture){
            $tipicoPlacement->addFixture($fixture);
        }

        return $tipicoPlacement;
    }

    private function createNewInstance(): TipicoPlacement
    {
        return new TipicoPlacement();
    }
}
