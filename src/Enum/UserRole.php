<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRole: string
{
    case TECHNICIAN = 'ROLE_TECHNICIAN';
    case ADMIN = 'ROLE_ADMIN';
    case DEFAULT_USER = 'ROLE_USER';
}
