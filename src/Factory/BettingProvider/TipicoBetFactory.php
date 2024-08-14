<?php

namespace App\Factory\BettingProvider;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TipicoBet>
 */
final class TipicoBetFactory extends PersistentProxyObjectFactory
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
        return TipicoBet::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'awayTeamName' => self::faker()->text(10),
            'endScoreAway' => self::faker()->numberBetween(0, 5),
            'endScoreHome' => self::faker()->numberBetween(0, 5),
            'finished' => self::faker()->boolean(95),
            'homeTeamName' => self::faker()->text(10),
            'oddAway' => self::faker()->randomFloat(2, 1.1, 6.0),
            'oddDraw' => self::faker()->randomFloat(2, 1.1, 6.0),
            'oddHome' => self::faker()->randomFloat(2, 1.1, 6.0),
            'result' => self::faker()->randomElement(BetOn::cases()),
            'startAtTimeStamp' => self::faker()->randomNumber(),
            'tipicoAwayTeamId' => self::faker()->randomNumber(),
            'tipicoHomeTeamId' => self::faker()->randomNumber(),
            'tipicoId' => self::faker()->randomNumber(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(TipicoBet $tipicoBet): void {})
            ;
    }
}
