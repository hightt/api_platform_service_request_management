<?php

declare(strict_types=1);

namespace App\Application\Ticket\Query;

final readonly class TicketRowResult
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $status,
        public string $priority,
        public string $createdAt,
        public ?string $serialNumber
    ) {
    }
}