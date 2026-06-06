<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TicketHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketHistoryRepository::class)]
class TicketHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\Column(length: 255)]
    private ?string $oldStatus = null;

    #[ORM\Column(length: 255)]
    private ?string $newStatus = null;

    #[ORM\Column]
    private ?DateTimeImmutable $changedAt = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Techician $createdBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function setOldStatus(string $oldStatus): static
    {
        $this->oldStatus = $oldStatus;

        return $this;
    }

    public function getNewStatus(): ?string
    {
        return $this->newStatus;
    }

    public function setNewStatus(string $newStatus): static
    {
        $this->newStatus = $newStatus;

        return $this;
    }

    public function getChangedAt(): ?DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    public function getCreatedBy(): ?Techician
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Techician $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
