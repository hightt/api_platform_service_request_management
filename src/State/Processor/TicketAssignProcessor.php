<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Ticket\Command\AssignTechnicianCommand;
use App\Dto\Ticket\TicketAssignInput;
use App\Entity\Ticket;
use InvalidArgumentException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class TicketAssignProcessor implements ProcessorInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Ticket
    {
        if (!$data instanceof TicketAssignInput) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s, %s given.', TicketAssignInput::class, get_debug_type($data)));
        }

        $ticketId = (int) ($uriVariables['id'] ?? 0);
        $updatedTicket = $this->handle(new AssignTechnicianCommand(ticketId: $ticketId, technicianId: $data->technicianId));

        return $updatedTicket;
    }
}
