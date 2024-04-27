<?php

namespace App\Service\BettingProvider;

enum BettingProvider: string
{
    case TIPICO = 'tipico';
    case BETANO = 'betano';
    case BWIN = 'bwin';

    public function getProviderIdent(): string
    {
        match ($this) {
            self::BETANO => $ident = 'betanoId',
            self::BWIN => $ident = 'bwinId',
            self::TIPICO => throw new \Exception('To be implemented'),
        };
        return $ident;
    }
}
