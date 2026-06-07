<?php

declare(strict_types=1);

namespace App\Service\TicketHistory;

use App\Entity\TicketHistory;
use App\Event\TicketStatusChangedEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class TicketHistoryCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {}

    public function createAfterTicketStatusChangedEvent(TicketStatusChangedEvent $event)
    {
        $ticket = $event->getTicket();

        $history = new TicketHistory();
        $history
            ->setTicket($ticket)
            ->setOldStatus($event->getOldStatus()?->value)
            ->setNewStatus($event->getNewStatus()->value)
            ->setChangedAt(new DateTimeImmutable())
        ;

        $this->entityManager->persist($history);
        $this->entityManager->flush();
    }
}
