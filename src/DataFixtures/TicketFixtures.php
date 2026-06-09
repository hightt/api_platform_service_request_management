<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Device;
use App\Entity\Technician;
use App\Entity\Ticket;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public const TICKET_PREFIX = 'ticket-';

    /**
    * @var array<int, string>
    */
    private array $titles = [
        'Broken screen after drop', 'Battery replacement request', 'OS Reinstallation',
        'Liquid spill recovery', 'Keyboard key not responding', 'Overheating under load',
        'Data recovery from dead SSD', 'Wi-Fi connectivity drops', 'No power on attempt',
        'Firmware upgrade failure'
    ];

    public function load(ObjectManager $manager): void
    {
        $statuses = [
            TicketStatus::NEW, TicketStatus::ASSIGNED, TicketStatus::IN_PROGRESS, 
            TicketStatus::DONE, TicketStatus::CANCELLED, TicketStatus::NEW, 
            TicketStatus::IN_PROGRESS, TicketStatus::DONE, TicketStatus::ASSIGNED, TicketStatus::DONE
        ];

        $priorities = [
            TicketPriority::HIGH, TicketPriority::MEDIUM, TicketPriority::LOW, 
            TicketPriority::CRITICAL, TicketPriority::LOW, TicketPriority::MEDIUM, 
            TicketPriority::HIGH, TicketPriority::LOW, TicketPriority::CRITICAL, TicketPriority::MEDIUM
        ];

        for ($i = 1; $i <= 10; $i++) {
            /** @var Device $device */
            $device = $this->getReference(DeviceFixtures::DEVICE_PREFIX . $i, Device::class);
            
            $ticket = new Ticket();
            $ticket->setTitle($this->titles[$i - 1]);
            $ticket->setDescription(sprintf('Detailed log description for issue: %s. Requires technical inspection.', strtolower($this->titles[$i - 1])));
            $ticket->setPriority($priorities[$i - 1]);
            $ticket->setStatus($statuses[$i - 1]);
            $ticket->setDevice($device);

            if (in_array($ticket->getStatus(), [TicketStatus::ASSIGNED, TicketStatus::IN_PROGRESS, TicketStatus::DONE], true)) {
                $techIndex = (($i - 1) % 5) + 1;
                /** @var Technician $tech */
                $tech = $this->getReference(TechnicianFixtures::TECH_ACTIVE_PREFIX . $techIndex, Technician::class);
                $ticket->setAssignedTechnician($tech);
            }

            if ($ticket->getStatus() === TicketStatus::DONE) {
                $now = new \DateTimeImmutable();
                $hoursAgo = $i * 2; 
                $ticket->setCreatedAt($now->modify(sprintf('-%d hours', $hoursAgo)));
                $ticket->setClosedAt($now);
            }

            $manager->persist($ticket);
            $this->addReference(self::TICKET_PREFIX . $i, $ticket);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DeviceFixtures::class,
            TechnicianFixtures::class,
        ];
    }
}