<?php

declare(strict_types=1);

namespace App\Enum;

enum TicketStatus: string
{
    case NEW = 'NEW';
    case ASSIGNED = 'ASSIGNED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case DONE = 'DONE';
    case CANCELLED = 'CANCELLED';
}
