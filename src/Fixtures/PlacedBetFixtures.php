<?php
declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\PlacedBet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class PlacedBetFixtures extends Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $placedBets = ['Books'];

        foreach ($placedBets as $placedBetData) {
            $placedBet = new PlacedBet($placedBetData);
            $manager->persist($placedBet);
            $manager->flush();

            $this->addReference(sprintf('placed-bet-%s', $placedBetData), $placedBet);
        }
    }
}
