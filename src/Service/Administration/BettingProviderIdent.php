<?php

namespace App\Service\Administration;

enum BettingProviderIdent: string
{
    case TIPICO = 'tipico';
    case BETANO = 'betano';
}
