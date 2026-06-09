<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Technician;
use App\Entity\Ticket;
use App\Enum\TicketStatus;
use App\Repository\TicketHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketHistoryRepository::class)]
#[ORM\Table(name: 'ticket_history')]
#[ApiResource(
    operations: [
        new GetCollection(
            forceEager: true,
            security: "is_granted('ROLE_TECHNICIAN')",
            openapi: new OpenApiOperation(
                summary: 'Retrieve all ticket history logs',
                description: 'Returns a complete audit log of ticket status transitions. Restricted to technicians.'
            )
        ),
        new Get(
            security: "is_granted('ROLE_TECHNICIAN')",
            openapi: new OpenApiOperation(
                summary: 'Get details of a specific history log',
                description: 'Returns a single audit entry by its ID.'
            )
        ),
    ],
    normalizationContext: ['groups' => ['ticketHistory:read']],
)]
class TicketHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ticketHistory:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ticketHistory:read'])]
    #[Assert\NotNull(message: 'History log must be attached to a valid ticket.')]
    private ?Ticket $ticket = null;

    #[ORM\Column(length: 255, nullable: true, enumType: TicketStatus::class)]
    #[Groups(['ticketHistory:read'])]
    private ?TicketStatus $oldStatus = null;

    #[ORM\Column(length: 255, enumType: TicketStatus::class)]
    #[Groups(['ticketHistory:read'])]
    #[Assert\NotNull(message: 'The new status value cannot be blank.')]
    private ?TicketStatus $newStatus = null;

    #[ORM\Column]
    #[Groups(['ticketHistory:read'])]
    #[Assert\NotNull(message: 'The timestamp of the change must be set.')]
    private ?DateTimeImmutable $changedAt = null;

    #[ORM\ManyToOne(targetEntity: Technician::class, inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['ticketHistory:read'])]
    private ?Technician $createdBy = null;

    public function __construct()
    {
        $this->changedAt = new DateTimeImmutable();
    }

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

    public function getOldStatus(): ?TicketStatus
    {
        return $this->oldStatus;
    }

    public function setOldStatus(?TicketStatus $oldStatus): static
    {
        $this->oldStatus = $oldStatus;

        return $this;
    }

    public function getNewStatus(): ?TicketStatus
    {
        return $this->newStatus;
    }

    public function setNewStatus(TicketStatus $newStatus): static
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

    public function getCreatedBy(): ?Technician
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Technician $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}