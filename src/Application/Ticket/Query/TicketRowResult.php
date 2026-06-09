<?php

declare(strict_types=1);

namespace App\Application\Ticket\Query;

use Symfony\Component\Serializer\Attribute\Groups;

final readonly class TicketRowResult
{
    public function __construct(
        #[Groups(['ticket:read'])]
        public int $id,
        #[Groups(['ticket:read'])]
        public string $title,
        #[Groups(['ticket:read'])]
        public string $description,
        #[Groups(['ticket:read'])]
        public string $status,
        #[Groups(['ticket:read'])] 
        public string $priority,
        #[Groups(['ticket:read'])]
        public string $createdAt,
        #[Groups(['ticket:read'])]
        public ?string $serialNumber
    ) {
    }
}