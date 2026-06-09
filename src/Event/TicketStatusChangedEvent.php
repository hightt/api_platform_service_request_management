<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Ticket;
use App\Enum\TicketStatus;

class TicketStatusChangedEvent
{
    public function __construct(
        private Ticket $ticket,
        private ?TicketStatus $oldStatus,
        private TicketStatus $newStatus,
    ) {
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getOldStatus(): ?TicketStatus
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): TicketStatus
    {
        return $this->newStatus;
    }
}