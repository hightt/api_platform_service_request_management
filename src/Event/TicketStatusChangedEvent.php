<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Ticket;

class TicketStatusChangedEvent
{
    public function __construct(
        private Ticket $ticket,
        private ?string $oldStatus,
        private string $newStatus,
    ) {
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }
}