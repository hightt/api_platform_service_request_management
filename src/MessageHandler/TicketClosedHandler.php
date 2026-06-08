<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\TicketClosedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TicketClosedHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function __invoke(TicketClosedMessage $event): void
    {
        $this->logger->info(sprintf(
            '[Email Simulator] Sending notification: Ticket #%d has been successfully closed.',
            $event->getTicketId()
        ));
        
    }
}