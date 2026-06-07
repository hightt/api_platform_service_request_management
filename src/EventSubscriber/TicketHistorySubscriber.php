<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\TicketStatusChangedEvent;
use App\Service\TicketHistory\TicketHistoryCreateService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

class TicketHistorySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TicketHistoryCreateService $ticketHistoryCreateService,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TicketStatusChangedEvent::class => 'onTicketStatusChanged',
        ];
    }

    public function onTicketStatusChanged(TicketStatusChangedEvent $event): void
    {
        try {
            $this->ticketHistoryCreateService->createAfterTicketStatusChangedEvent($event);
        } catch (Throwable $e) {
            $this->logger->critical('An error occured during ticket history create event', ['e' => json_encode($e)]);
        }
    }
}