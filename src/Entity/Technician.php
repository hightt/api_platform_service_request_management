<?php

declare (strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Dto\Technician\TechnicianPerformanceOutput;
use App\Entity\Ticket;
use App\Entity\TicketHistory;
use App\Repository\TechnicianRepository;
use App\State\Provider\TechnicianPerformanceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TechnicianRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'A technician with this email address already exists.')]
#[ApiResource(
    normalizationContext:   ['groups' => ['technician:read']],
    denormalizationContext: ['groups' => ['technician:write']],
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/technicians/stats',
            status: 200,
            output: TechnicianPerformanceOutput::class,
            provider: TechnicianPerformanceProvider::class,
            normalizationContext: ['groups' => ['technician:stats']],
            paginationEnabled: false,
            openapi: new OpenApiOperation(
                summary: 'Get performance statistics for all technicians',
                description: 'Returns total closed tickets and average resolution time in hours for each technician.',
            ),
        ),
        new Get(),
        new Post(),
        new Patch(),
    ],
)]
class Technician
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['technician:read', 'ticketHistory:read', 'ticket:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['technician:read', 'technician:write', 'ticket:read'])]
    #[Assert\NotBlank(message: 'First name cannot be blank.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'First name must be at least {{ limit }} characters long.',
        maxMessage: 'First name cannot be longer than {{ limit }} characters.',
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Groups(['technician:read', 'technician:write', 'ticket:read'])]
    #[Assert\NotBlank(message: 'Last name cannot be blank.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Last name must be at least {{ limit }} characters long.',
        maxMessage: 'Last name cannot be longer than {{ limit }} characters.',
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['technician:read', 'technician:write'])]
    #[Assert\NotBlank(message: 'Email address cannot be blank.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email address.')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['technician:read', 'technician:write'])]
    #[Assert\NotNull(message: 'The active status must be specified (true or false).')]
    private ?bool $active = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'assignedTechnician')]
    private Collection $tickets;

    /**
     * @var Collection<int, TicketHistory>
     */
    #[ORM\OneToMany(targetEntity: TicketHistory::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $ticketHistories;

    #[ORM\OneToOne(inversedBy: 'technician', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $login = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->ticketHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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
            $ticket->setAssignedTechnician($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getAssignedTechnician() === $this) {
                $ticket->setAssignedTechnician(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketHistory>
     */
    public function getTicketHistories(): Collection
    {
        return $this->ticketHistories;
    }

    public function addTicketHistory(TicketHistory $ticketHistory): static
    {
        if (! $this->ticketHistories->contains($ticketHistory)) {
            $this->ticketHistories->add($ticketHistory);
            $ticketHistory->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTicketHistory(TicketHistory $ticketHistory): static
    {
        if ($this->ticketHistories->removeElement($ticketHistory)) {
            // set the owning side to null (unless already changed)
            if ($ticketHistory->getCreatedBy() === $this) {
                $ticketHistory->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getLogin(): ?User
    {
        return $this->login;
    }

    public function setLogin(User $login): static
    {
        $this->login = $login;

        return $this;
    }
}
