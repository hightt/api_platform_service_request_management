<?php

declare (strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Technician;
use App\Entity\Ticket;
use App\Repository\TicketHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TicketHistoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            forceEager: true,
        ),
        new Get(),
        new Post(),
        new Patch(),
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

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ticketHistory:read'])]
    private ?Ticket $ticket = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['ticketHistory:read'])]
    private ?string $oldStatus = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ticketHistory:read'])]
    private ?string $newStatus = null;

    #[ORM\Column]
    #[Groups(['ticketHistory:read'])]
    private ?DateTimeImmutable $changedAt = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['ticketHistory:read'])]
    private ?Technician $createdBy = null;

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

    public function setOldStatus(?string $oldStatus): static
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
