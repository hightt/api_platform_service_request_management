<?php

declare (strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Ticket;
use App\Enum\TicketStatus;
use App\Event\TicketStatusChangedEvent;
use App\Service\Ticket\TicketWorkflowService;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Workflow\WorkflowInterface;

class TicketStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private WorkflowInterface $ticketStatusStateMachine,
        private TicketWorkflowService $ticketWorkflowService,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Ticket
    {
        if (!$data instanceof Ticket) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, %s given.', Ticket::class, get_debug_type($data)));
        }

        if (is_null($data->getId())) {
            return $this->handleCreation($data, $operation, $uriVariables, $context);
        }

        /** @var Ticket $previousTicket */
        $previousTicket = $context['previous_data'];
        $oldStatus = $previousTicket->getStatus();
        $newStatus = $data->getStatus();

        if ($oldStatus === $newStatus) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $data->setStatus($oldStatus);
        [$allowed, $transitionName] = $this->ticketWorkflowService->isTicketStatusChangeAllowed($newStatus, $data);
        if (!$allowed) {
            $this->throwValidationError($data, $oldStatus, $newStatus);
        }
        $this->ticketStatusStateMachine->apply($data, $transitionName);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function handleCreation(Ticket $ticket, Operation $operation, array $uriVariables, array $context): Ticket
    {
        /** @var Ticket $result */
        $result = $this->persistProcessor->process($ticket, $operation, $uriVariables, $context);

        $this->eventDispatcher->dispatch(
            new TicketStatusChangedEvent($result, null, $result->getStatus()),
        );

        return $result;
    }

    private function throwValidationError(mixed $data, ?TicketStatus $oldStatus, TicketStatus $newStatus): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                sprintf('Ticket transition from status "%s" to "%s" is not allowed.', $oldStatus?->value, $newStatus->value),
                null,
                [],
                $data,
                'status',
                $newStatus->value,
            ),
        ]);

        throw new ValidationException($violations);
    }
}
