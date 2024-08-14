<?php

namespace App\Factory\BettingProvider;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SimulatorFavoriteList>
 */
final class SimulatorFavoriteListFactory extends PersistentProxyObjectFactory
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
        return SimulatorFavoriteList::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'bets' => self::faker()->randomNumber(),
            'created' => self::faker()->dateTime(),
            'identifier' => self::faker()->text(20),
            'totalCashBox' => self::faker()->randomFloat(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(SimulatorFavoriteList $simulatorFavoriteList): void {})
            ;
    }
}
