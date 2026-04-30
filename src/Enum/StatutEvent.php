<?php

namespace App\Enum;

enum StatutEvent: string
{
    case BROUILLON = 'brouillon';
    case PUBLIE = 'publie';
    case COMPLET = 'complet';
    case ANNULE = 'annule';
}
