<?php

declare (strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Ticket;
use App\Repository\DeviceRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['device:read']],
    denormalizationContext: ['groups' => ['device:write']],
)]
class Device
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['device:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['device:read', 'device:write'])]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['device:read', 'device:write'])]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Groups(['device:read', 'device:write'])]
    private ?string $customerName = null;

    #[ORM\Column]
    #[Groups(['device:read'])]
    private ?DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'device', orphanRemoval: true)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (! $this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setDevice($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getDevice() === $this) {
                $ticket->setDevice(null);
            }
        }

        return $this;
    }
}
