<?php

namespace App\Enum;

enum StatutInscription: string
{
    case CONFIRMEE= 'confirmee'; 
    case EN_ATTENTE= 'en_attente';
    case ANNULEE = 'annulee';
}
