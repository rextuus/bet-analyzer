<?php
declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\Spm\PlacedBet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


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
