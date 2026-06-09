<?php

declare(strict_types=1);

namespace App\Message;

class TicketClosedMessage
{
    public function __construct(
        private int $ticketId
    ) {}

    public function getTicketId(): int
    {
        return $this->ticketId;
    }
}