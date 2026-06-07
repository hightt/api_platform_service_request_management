<?php

declare(strict_types=1);

namespace App\Application\Ticket\QueryHandler;

use App\Application\Ticket\Query\GetTicketCollectionQuery;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetTicketCollectionHandler
{
    private const ALLOWED_SORT_FIELDS = [
        'id' => 't.id',
        'status' => 't.status',
        'priority' => 't.priority',
        'createdAt' => 't.created_at',
    ];

    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetTicketCollectionQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
                't.id', 
                't.title',
                't.description',
                't.status', 
                't.priority', 
                't.created_at as createdAt',
                'd.serial_number as serialNumber'
            )
            ->from('ticket', 't')
            ->leftJoin('t', 'device', 'd', 't.device_id = d.id')
        ;

        if ($query->status) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $query->status)
            ;
        }
        if ($query->priority) {
            $qb->andWhere('t.priority = :priority')
               ->setParameter('priority', $query->priority)
            ;
        }
        if ($query->serialNumber) {
            $qb->andWhere('d.serial_number LIKE :serialNumber')
               ->setParameter('serialNumber', '%' . $query->serialNumber . '%')
            ;
        }

        $countQb = clone $qb;
        $totalItems = (int) $countQb->select('COUNT(t.id)')->executeQuery()->fetchOne();

        $sortField = self::ALLOWED_SORT_FIELDS[$query->sortBy] ?? 't.id';
        $sortOrder = strtoupper($query->sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $qb->orderBy($sortField, $sortOrder);

        $offset = ($query->page - 1) * $query->itemsPerPage;
        $qb->setFirstResult($offset)
           ->setMaxResults($query->itemsPerPage);

        $data = $qb->executeQuery()->fetchAllAssociative();

        return [
            'data' => $data,
            'total_items' => $totalItems,
        ];
    }
}