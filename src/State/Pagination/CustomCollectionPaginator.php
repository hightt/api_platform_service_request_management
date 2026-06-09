<?php

declare(strict_types=1);

namespace App\State\Pagination;

use App\Application\Ticket\Query\TicketRowResult;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements PaginatorInterface<TicketRowResult>
 * @implements IteratorAggregate<mixed, TicketRowResult>
 */
class CustomCollectionPaginator implements PaginatorInterface, IteratorAggregate
{
    /**
     * @param list<TicketRowResult> $items
     */
    public function __construct(
        private array $items,
        private int $currentPage,
        private int $itemsPerPage,
        private int $totalItems
    ) {
    }

    public function getLastPage(): float
    {
        return ceil($this->totalItems / $this->itemsPerPage) ?: 1.0;
    }

    public function getTotalItems(): float
    {
        return (float) $this->totalItems;
    }

    public function getCurrentPage(): float
    {
        return (float) $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return (float) $this->itemsPerPage;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Traversable<mixed, TicketRowResult>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}