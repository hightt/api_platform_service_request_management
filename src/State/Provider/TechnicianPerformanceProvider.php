<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Technician\Query\GetTechnicianPerformanceQuery;
use App\Dto\Technician\TechnicianPerformanceOutput;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class TechnicianPerformanceProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $rawStats = $this->handle(new GetTechnicianPerformanceQuery());

        return array_map(fn (array $row) => new TechnicianPerformanceOutput(
            technicianId: (int) $row['technicianid'],
            name: (string) $row['name'],
            closedTickets: (int) $row['closedtickets'],
            averageClosingTimeHours: (float) $row['averageclosingtimehours'],
        ), $rawStats);
    }
}
