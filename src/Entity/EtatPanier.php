<?php

namespace App\Entity;

enum EtatPanier: string
{
    case EN_COURS= 'EN_COURS';
    case VALIDE='VALIDE';

}