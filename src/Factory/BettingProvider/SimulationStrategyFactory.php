<?php

namespace App\Factory\BettingProvider;

use App\Entity\BettingProvider\SimulationStrategy;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SimulationStrategy>
 */
final class SimulationStrategyFactory extends PersistentProxyObjectFactory
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
        return SimulationStrategy::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $jsonString = "{\"min\":4.4,\"max\":4.5,\"searchBetOn\":\"2\",\"targetBetOn\":\"2\",\"allowedWeekDays\":[6]}";
        $parameters = json_decode($jsonString, true);
        return [
            'identifier' => self::faker()->text(255),
            'parameters' => json_encode($parameters, true),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(SimulationStrategy $simulationStrategy): void {})
            ;
    }
}
