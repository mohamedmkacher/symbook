<?php

namespace App\Entity;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CLIENT = 'ROLE_CLIENT';
}