<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use App\api\Exception\PosterRepositoryInsertException;
use App\api\Model\PublicationModelFactory;
use App\api\Repository\PosterRepositoryImpl;
use Doctrine\DBAL\ParameterType;

class PosterRepositoryImplTest extends AbstractRepositoryTestCase
{
    protected PosterRepositoryImpl $repo;
    protected PublicationModelFactory $modelFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new PosterRepositoryImpl($this->connection);
    }

    public function testFindPosterId()
    {
        // GIVEN
        $this->connection
            ->expects(self::exactly(2))
            ->method('fetchOne')
            ->withConsecutive(
                [
                    "SELECT id FROM posters WHERE md5 = :md5",
                    ['md5' => md5('url1')],
                    ['md5' => ParameterType::STRING]
                ],
                [
                    "SELECT id FROM posters WHERE md5 = :md5",
                    ['md5' => md5('url2')],
                    ['md5' => ParameterType::STRING]
                ]
            )
            ->willReturnOnConsecutiveCalls(false, 55);

        // WHEN, THEN
        self::assertSame(null, $this->repo->findPosterId('url1'));
        self::assertSame(55, $this->repo->findPosterId('url2'));
    }

    public function testInsertPoster_success()
    {
        $this->expectPosterInsert('someUrl');
        $this->expectLastInsertId(55);

        $this->assertSame(55, $this->repo->insertPoster('someUrl'));
    }

    public function testInsertPoster_fail()
    {
        $this->expectPosterInsert('someUrl');
        $this->expectLastInsertId(false);

        self::expectException(PosterRepositoryInsertException::class);

        $this->repo->insertPoster('someUrl');
    }

    protected function expectLastInsertId(int|false $expectedLastInsertId): void
    {
        $this->connection
            ->expects(self::once())
            ->method('lastInsertId')
            ->willReturn($expectedLastInsertId);
    }

    protected function expectPosterInsert(string $dataUrl): void
    {
        $this->connection
            ->expects(self::once())
            ->method('executeStatement')
            ->with(
                "INSERT INTO posters (md5, url) values (:md5, :url)",
                [
                    'md5' => md5($dataUrl),
                    'url' => $dataUrl,
                ],
                [
                    'md5' => ParameterType::STRING,
                    'url' => ParameterType::STRING,
                ]
            );
    }
}
