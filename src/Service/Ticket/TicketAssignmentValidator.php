<?php

declare(strict_types=1);

namespace App\Service\Ticket;

use App\Application\Ticket\Command\AssignTechnicianCommand;
use App\Entity\Technician;
use App\Entity\Ticket;
use App\Trait\ValidationExceptionTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketAssignmentValidator
{
    use ValidationExceptionTrait;

    public function validateBeforeAssign(
        AssignTechnicianCommand $command,
        ?Ticket $ticket,
        ?Technician $technician,
    ): void {
        if (!$ticket) {
            throw new NotFoundHttpException(sprintf('Ticket with ID %d not found.', $command->ticketId));
        }

        if (!$technician) {
            $this->throwValidationError('technicianId', sprintf('Technician with ID %d does not exist.', $command->technicianId));
        }

        if (!$technician->isActive()) {
            $this->throwValidationError('technicianId', 'The selected technician is currently inactive.');
        }
    }
}
