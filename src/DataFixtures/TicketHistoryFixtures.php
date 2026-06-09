<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Technician;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Enum\TicketStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketHistoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            /** @var Ticket $ticket */
            $ticket = $this->getReference(TicketFixtures::TICKET_PREFIX . $i, Ticket::class);
            
            /** @var Technician|null $tech */
            $tech = $ticket->getAssignedTechnician();

            $historyInit = new TicketHistory();
            $historyInit->setTicket($ticket);
            $historyInit->setOldStatus(null); 
            $historyInit->setNewStatus(TicketStatus::NEW);
            $historyInit->setChangedAt($ticket->getCreatedAt() ?? new \DateTimeImmutable('-2 days'));
            $historyInit->setCreatedBy($tech); 
            $manager->persist($historyInit);

            if ($ticket->getStatus() !== TicketStatus::NEW) {
                $historyTransition = new TicketHistory();
                $historyTransition->setTicket($ticket);
                $historyTransition->setOldStatus(TicketStatus::NEW);
                $historyTransition->setNewStatus($ticket->getStatus());
                
                $changedAt = $ticket->getClosedAt() ?? ($ticket->getCreatedAt()?->modify('+1 hour') ?: new \DateTimeImmutable());
                $historyTransition->setChangedAt($changedAt);
                $historyTransition->setCreatedBy($tech);
                
                $manager->persist($historyTransition);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TicketFixtures::class,
        ];
    }
}