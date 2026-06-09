<?php

declare(strict_types=1);

namespace App\Dto\Technician;

use Symfony\Component\Serializer\Attribute\Groups;

class TechnicianPerformanceOutput
{
    public function __construct(
        #[Groups(['technician:stats'])]
        public readonly int $technicianId,
        #[Groups(['technician:stats'])]
        public readonly string $name,
        #[Groups(['technician:stats'])]
        public readonly int $closedTickets,
        #[Groups(['technician:stats'])]
        public readonly float $averageClosingTimeHours,
    ) {
    }
}
