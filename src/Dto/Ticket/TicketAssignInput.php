<?php

declare(strict_types=1);

namespace App\Dto\Ticket;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class TicketAssignInput
{
    #[Groups(['ticket:write'])]
    #[Assert\NotBlank(message: 'Technician ID is required.')]
    #[Assert\GreaterThan(0, message: 'Invalid technician ID.')]
    public ?int $technicianId = null;

}