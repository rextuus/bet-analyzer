<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\OddOutcome;

use App\Entity\OddOutcome;
use App\Service\Statistic\Content\OddOutcome\Data\OddOutcomeData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class OddOutcomeFactory
{
    public function createByData(OddOutcomeData $data): OddOutcome
    {
        $oddOutcome = $this->createNewInstance();
        $this->mapData($data, $oddOutcome);
        return $oddOutcome;
    }

    public function mapData(OddOutcomeData $data, OddOutcome $oddOutcome): OddOutcome
    {
        $oddOutcome->setCorrectOutcomes($data->getCorrectOutcomes());
        $oddOutcome->setMax($data->getMax());
        $oddOutcome->setMin($data->getMin());
        $oddOutcome->setFixtureAmount($data->getFixtureAmount());
        $oddOutcome->setBetOn($data->getBetOn());

        foreach ($data->getFixtures() as $fixture){
            $oddOutcome->addFixture($fixture);
        }

        return $oddOutcome;
    }

    private function createNewInstance(): OddOutcome
    {
        return new OddOutcome();
    }
}
