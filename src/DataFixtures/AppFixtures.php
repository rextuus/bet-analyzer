<?php

namespace App\DataFixtures;

use App\Factory\BettingProvider\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        UserFactory::createOne(['email' => 'admin@test.com']);
//        UserFactory::createMany(10);
//
//        $simulators = SimulatorFactory::createMany(60);
//
//        SimulatorFavoriteListFactory::createMany(5, function () use ($simulators) {
//            return [
//                'addSimulator' => $simulators[array_rand($simulators)]
//            ];
//        });
//
//
//        TipicoBetFactory::createMany(100);

        $manager->flush();
    }
}
