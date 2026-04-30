<?php

namespace App\Enum;

enum Categorie: string
{
    case CONFERENCE = 'conference';
    case ATELIER = 'atelier';
    case MEETUP = 'meetup';
    case FORMATION = 'formation';
    case CONCERT = 'concert';
}
