<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Technician;

use App\Application\Technician\Query\GetTechnicianPerformanceQuery;
use App\Application\Technician\QueryHandler\GetTechnicianPerformanceHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GetTechnicianPerformanceHandlerTest extends KernelTestCase
{
    public function testHandlerReturnsArrayStructure(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var GetTechnicianPerformanceHandler $handler */
        $handler = $container->get(GetTechnicianPerformanceHandler::class);

        $result = $handler(new GetTechnicianPerformanceQuery());

        self::assertIsArray($result);

        foreach ($result as $row) {
            self::assertArrayHasKey('technicianid', $row);
            self::assertArrayHasKey('name', $row);
            self::assertArrayHasKey('closedtickets', $row);
            self::assertArrayHasKey('averageclosingtimehours', $row);

            self::assertIsNumeric($row['closedtickets']);
            self::assertIsNumeric($row['averageclosingtimehours']);
        }
    }
}
