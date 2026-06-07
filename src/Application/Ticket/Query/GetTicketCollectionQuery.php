<?php

declare(strict_types=1);

namespace App\Application\Ticket\Query;

class GetTicketCollectionQuery
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?string $serialNumber = null,
        public readonly string $sortBy = 'id',
        public readonly string $sortOrder = 'DESC',
        public readonly int $page = 1,
        public readonly int $itemsPerPage = 30,
    ) {
    }
}