<?php

namespace App\Factory\BettingProvider;

use App\Entity\BettingProvider\Simulator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Simulator>
 */
final class SimulatorFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Simulator::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'cashBox' => self::faker()->randomFloat(2, 59, 250),
            'currentIn' => 1.0,
            'identifier' => self::faker()->realTextBetween(10, 20),
            'strategy' => SimulationStrategyFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Simulator $simulator): void {})
            ;
    }
}
