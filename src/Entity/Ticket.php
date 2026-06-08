<?php

declare (strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use App\Dto\Ticket\TicketAssignInput;
use App\Entity\Device;
use App\Entity\Technician;
use App\Entity\TicketHistory;
use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use App\Repository\TicketRepository;
use App\Security\Voter\TicketAccessVoter;
use App\State\Processor\TicketAssignProcessor;
use App\State\Processor\TicketStateProcessor;
use App\State\Provider\TicketCollectionProvider;
use ArrayObject;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            forceEager: true,
            provider: TicketCollectionProvider::class,
            security: "is_granted('ROLE_TECHNICIAN')",
            parameters: [
                'status' => new QueryParameter(property: 'status', description: 'Filter by ticket status'),
                'priority' => new QueryParameter(property: 'priority', description: 'Filter by ticket priority'),
                'serialNumber' => new QueryParameter(property: 'serialNumber', description: 'Search by serial number (partial match)'),
                'page' => new QueryParameter(property: 'page', description: 'Page number'),
                'itemsPerPage' => new QueryParameter(property: 'itemsPerPage', description: 'Number of items per page'),
                'order' => new QueryParameter(property: 'order', description: 'Sort by field, e.g. order[createdAt]=desc'),
            ]
        ),
        new Get(
            security: "is_granted('ROLE_TECHNICIAN')",
        ),
        new Post(
            processor: TicketStateProcessor::class,
            security: "is_granted('ROLE_TECHNICIAN')"
        ),
        new Patch(
            processor: TicketStateProcessor::class,
            security: "is_granted('" . TicketAccessVoter::EDIT . "', object)",
        ),
        new Post(
            uriTemplate: '/tickets/{id}/assign',
            security: "is_granted('ROLE_ADMIN')",
            status: Response::HTTP_OK,
            input: TicketAssignInput::class,
            processor: TicketAssignProcessor::class,
            openapi: new OpenApiOperation(
                summary: 'Assign a technician to a ticket',
                description: 'Validates technician status, updates ticket to ASSIGNED, and creates a history log.',
                requestBody: new RequestBody(
                    description: 'The assignment payload containing the technician ID',
                    content: new ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'technicianId' => [
                                        'type' => 'integer',
                                        'example' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ]),
                    required: true,
                ),
                responses: [
                    Response::HTTP_OK => new OpenApiResponse(
                        description: 'Technician successfully assigned',
                    ),
                    Response::HTTP_UNPROCESSABLE_ENTITY => new OpenApiResponse(
                        description: 'Validation or Workflow constraint violation',
                    ),
                ],
            ),
        ),
    ],
    normalizationContext: ['groups' => ['ticket:read']],
    denormalizationContext: ['groups' => ['ticket:write']],
)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ticket:read', 'ticketHistory:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ticket:read', 'ticket:write'])]
    #[Assert\NotBlank(message: 'Ticket title cannot be blank.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Ticket title must be at least {{ limit }} characters long.',
        maxMessage: 'Ticket title cannot be longer than {{ limit }} characters.',
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['ticket:read', 'ticket:write'])]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'Description cannot be longer than {{ limit }} characters.',
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255, enumType: TicketPriority::class)]
    #[Groups(['ticket:read', 'ticket:write'])]
    #[Assert\NotNull(message: 'Ticket priority must be specified.')]
    #[Assert\Type(type: TicketPriority::class, message: 'Invalid priority value provided.')]
    private ?TicketPriority $priority = null;

    #[ORM\Column(length: 255, enumType: TicketStatus::class)]
    #[Groups(['ticket:read', 'ticket:write'])]
    #[Assert\NotNull(message: 'Ticket status must be specified.')]
    #[Assert\Type(type: TicketStatus::class, message: 'Invalid status value provided.')]
    private ?TicketStatus $status = null;

    #[ORM\Column]
    #[Groups(['ticket:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['ticket:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['ticket:read'])]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[Groups(['ticket:read'])]
    private ?Technician $assignedTechnician = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?Device $device = null;

    /**
     * @var Collection<int, TicketHistory>
     */
    #[ORM\OneToMany(targetEntity: TicketHistory::class, mappedBy: 'ticket')]
    private Collection $ticketHistories;

    public function __construct()
    {
        $this->ticketHistories = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): ?TicketPriority
    {
        return $this->priority;
    }

    public function setPriority(TicketPriority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?TicketStatus
    {
        return $this->status;
    }

    /* We allow to accept both Enum (from API Platform) and string (from Workflow component) */
    public function setStatus(TicketStatus|string $status): static
    {
        $this->status = is_string($status) ? TicketStatus::from($status) : $status;

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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getAssignedTechnician(): ?Technician
    {
        return $this->assignedTechnician;
    }

    public function setAssignedTechnician(?Technician $assignedTechnician): static
    {
        $this->assignedTechnician = $assignedTechnician;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): static
    {
        $this->device = $device;

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
            $ticketHistory->setTicket($this);
        }

        return $this;
    }

    public function removeTicketHistory(TicketHistory $ticketHistory): static
    {
        if ($this->ticketHistories->removeElement($ticketHistory)) {
            if ($ticketHistory->getTicket() === $this) {
                $ticketHistory->setTicket(null);
            }
        }

        return $this;
    }
}
