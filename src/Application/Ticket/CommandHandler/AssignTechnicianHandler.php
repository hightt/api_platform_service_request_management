<?php

declare(strict_types=1);

namespace App\Application\Ticket\CommandHandler;

use App\Application\Ticket\Command\AssignTechnicianCommand;
use App\Entity\Ticket;
use App\Enum\TicketStatus;
use App\Event\TicketStatusChangedEvent;
use App\Repository\TechnicianRepository;
use App\Repository\TicketRepository;
use App\Service\Ticket\TicketAssignmentValidator;
use App\Service\Ticket\TicketWorkflowService;
use App\Trait\ValidationExceptionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class AssignTechnicianHandler
{
    use ValidationExceptionTrait;

    public const TARGET_STATUS = TicketStatus::ASSIGNED;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TicketRepository $ticketRepository,
        private TechnicianRepository $technicianRepository,
        private WorkflowInterface $ticketStatusStateMachine,
        private TicketWorkflowService $ticketWorkflowService,
        private EventDispatcherInterface $eventDispatcher,
        private TicketAssignmentValidator $ticketAssignmentValidator,
    ) {
    }

    public function __invoke(AssignTechnicianCommand $command): Ticket
    {
        $ticket = $this->ticketRepository->find($command->ticketId);
        $technician = $this->technicianRepository->find($command->technicianId);
        $this->ticketAssignmentValidator->validateBeforeAssign($command, $ticket, $technician);

        $oldStatus = $ticket->getStatus();
        [$allowed, $transitionName] = $this->ticketWorkflowService->isTicketStatusChangeAllowed(self::TARGET_STATUS, $ticket);
        if (!$allowed) {
            $this->throwValidationError('status', sprintf('Ticket transition from status "%s" to "%s" is not allowed.', $oldStatus->value, self::TARGET_STATUS->value));
        }

        $ticket->setAssignedTechnician($technician);
        $this->ticketStatusStateMachine->apply($ticket, $transitionName);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new TicketStatusChangedEvent($ticket, $oldStatus, self::TARGET_STATUS));

        return $ticket;
    }
}