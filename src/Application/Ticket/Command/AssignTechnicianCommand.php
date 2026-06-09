<?php

declare(strict_types=1);

namespace App\Application\Ticket\Command;

class AssignTechnicianCommand
{
    public function __construct(
        public readonly int $ticketId,
        public readonly int $technicianId,
    ) {
    }
}