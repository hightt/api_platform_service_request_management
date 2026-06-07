<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\Ticket\Query\GetTicketCollectionQuery;
use App\State\Pagination\CustomCollectionPaginator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class TicketCollectionProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CustomCollectionPaginator
    {
        $filters = $context['filters'] ?? [];
        $page = (int) ($filters['page'] ?? 1);
        $itemsPerPage = (int) ($filters['itemsPerPage'] ?? 30);
        $sortData = $filters['order'] ?? [];
        $sortBy = (string) (key($sortData) ?: 'id');
        $sortOrder = (string) (current($sortData) ?: 'DESC');

        $query = new GetTicketCollectionQuery(
            status: $filters['status'] ?? null,
            priority: $filters['priority'] ?? null,
            serialNumber: $filters['serialNumber'] ?? null,
            sortBy: $sortBy,
            sortOrder: $sortOrder,
            page: $page,
            itemsPerPage: $itemsPerPage
        );

        $result = $this->handle($query);

        return new CustomCollectionPaginator(
            items: $result['data'],
            currentPage: $page,
            itemsPerPage: $itemsPerPage,
            totalItems: $result['total_items']
        );
    }
}