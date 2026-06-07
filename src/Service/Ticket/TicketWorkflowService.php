<?php

declare (strict_types=1);

namespace App\Service\Ticket;

use Symfony\Component\Workflow\WorkflowInterface;

class TicketWorkflowService
{
    public function __construct(
        private readonly WorkflowInterface $ticketStatusStateMachine,
    ) {
    }

    /**
     * @return array{0: bool, 1: string|null}
     */
    public function isTicketStatusChangeAllowed(?string $newStatus, mixed $data): array
    {
        foreach ($this->ticketStatusStateMachine->getEnabledTransitions($data) as $transition) {
            if (in_array($newStatus, (array) $transition->getTos(), true)) {
                return [true, $transition->getName()];
            }
        }

        return [false, null];
    }
}
