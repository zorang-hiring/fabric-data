<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractRepositoryTestCase extends TestCase
{
    protected Connection|MockObject $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->mockDbConnection();
    }

    private function mockDbConnection(): mixed
    {
        return self::getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}