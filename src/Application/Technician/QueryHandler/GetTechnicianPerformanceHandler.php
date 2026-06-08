<?php

declare(strict_types=1);

namespace App\Application\Technician\QueryHandler;

use App\Application\Technician\Query\GetTechnicianPerformanceQuery;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetTechnicianPerformanceHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetTechnicianPerformanceQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            'tech.id as technicianId',
            "CONCAT(tech.first_name, ' ', tech.last_name) as name",
            'COUNT(t.id) as closedTickets',
            'COALESCE(ROUND(CAST(AVG(EXTRACT(EPOCH FROM (t.closed_at - t.created_at))) / 3600 AS numeric), 1), 0.0) as averageClosingTimeHours',
        )
            ->from('technician', 'tech')
            ->leftJoin('tech', 'ticket', 't', "t.assigned_technician_id = tech.id AND t.status = 'DONE'")
            ->groupBy('tech.id')
            ->orderBy('closedTickets', 'DESC');

        return $qb->executeQuery()->fetchAllAssociative();
    }
}
