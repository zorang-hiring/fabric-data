<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\Repository\PublicationRepositoryImpl;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Connection;

class PublicationRepositoryImplTest extends TestCase
{
    protected PublicationRepositoryImpl $storage;
    protected Connection|MockObject $connection;
    protected PublicationModelFactory $modelFactory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new PublicationModelFactory();
        $this->connection = $this->mockDbConnection();
        $this->storage = new PublicationRepositoryImpl(
            $this->connection,
            $this->modelFactory
        );
    }

    public function testSearchByTitle()
    {
        // EXPECTED
        $this->connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                "SELECT pu.externalId, pu.title, pu.year, pu.type, po.url as poster "
                . "FROM publications pu "
                . "LEFT JOIN posters po ON pu.poster_id = po.id"
                . "WHERE pu.title LIKE '%:title%'",
                ['title' => 'some title'],
                ['title' => ParameterType::STRING]
            )
            ->willReturn([
                [
                    'id' => '1',
                    'externalId' => 'externId1',
                    'title' => 'title1',
                    'year' => '2001',
                    'type' => 'type1',
                    'poster' => 'poster1',
                ],
                [
                    'id' => '2',
                    'externalId' => 'externId2',
                    'title' => 'title2',
                    'year' => '2002',
                    'type' => 'type2',
                    'poster' => 'poster2',
                ]
            ]);

        // WHEN
        $result = $this->storage->searchByTitle('some title');

        // THEN
        self::assertEquals(
            $result,
            (new PublicationModelCollection())
                ->addItem($this->modelFactory->makeOne([
                    'externalId' => 'externId1',
                    'type' => 'type1',
                    'title' => 'title1',
                    'year' => '2001',
                    'poster' => 'poster1',
                ]))
                ->addItem($this->modelFactory->makeOne([
                    'externalId' => 'externId2',
                    'type' => 'type2',
                    'title' => 'title2',
                    'year' => '2002',
                    'poster' => 'poster2',
                ]))
        );
    }

    public function testSave()
    {
        // GIVEN
        $publications = new PublicationModelCollection();
        $publications
            ->addItem($this->modelFactory->makeOne([
                'externalId' => 'externId1',
                'type' => 'type1',
                'title' => 'title1',
                'year' => '2001',
                'poster' => 'poster13',
            ]))
            ->addItem($this->modelFactory->makeOne([
                'externalId' => 'externId2',
                'type' => 'type2',
                'title' => 'title2',
                'year' => '2002',
                'poster' => 'poster2'
            ]))
            ->addItem($this->modelFactory->makeOne([
                'externalId' => 'externId3',
                'type' => 'type3',
                'title' => 'title3',
                'year' => '2003',
                'poster' => 'poster13'
            ]));;


        // EXPECTED
        $typesFindPoster = [
            'md5' => ParameterType::STRING
        ];
        $sqlFindPoster = "SELECT id FROM posters WHERE md5 = :md5";
        $typesInsertPoster = [
            'md5' => ParameterType::STRING,
            'url' => ParameterType::STRING,
        ];
        $sqlInsertPoster = "INSERT INTO posters (md5, url) values (:md5, :url)";
        $typesReplacePublication = [
            'externalId' => ParameterType::STRING,
            'title' => ParameterType::STRING,
            'year' => ParameterType::STRING,
            'type' => ParameterType::STRING,
            'poster_id' => ParameterType::INTEGER
        ];
        $sqlReplacePublication =
            "REPLACE INTO publications (externalId, title, year, type, poster_id) values "
            . "(:externalId, :title, :year, :type, :poster_id)";

        $this->connection
            ->expects(self::exactly(3))
            ->method('fetchOne')
            ->withConsecutive(
                [
                    $sqlFindPoster,
                    ['md5' => md5('poster13')],
                    $typesFindPoster
                ],
                [
                    $sqlFindPoster,
                    ['md5' => md5('poster2')],
                    $typesFindPoster
                ],
                [
                    $sqlFindPoster,
                    ['md5' => md5('poster13')],
                    $typesFindPoster
                ]
            )
            ->willReturnOnConsecutiveCalls(false, false, ['id' => 1]);

        $this->connection
            ->expects(self::exactly(2))
            ->method('lastInsertId')
            ->willReturnOnConsecutiveCalls(1, 2);

        $this->connection
            ->expects(self::exactly(5))
            ->method('executeStatement')
            ->withConsecutive(
                [
                    $sqlInsertPoster,
                    [
                        'md5' => md5('poster13'),
                        'url' => 'poster13',
                    ],
                    $typesInsertPoster
                ],
                [
                    $sqlReplacePublication,
                    [
                        'externalId' => 'externId1',
                        'title' => 'title1',
                        'year' => 2001,
                        'type' => 'type1',
                        'poster_id' => 1
                    ],
                    $typesReplacePublication
                ],
                [
                    $sqlInsertPoster,
                    [
                        'md5' => md5('poster2'),
                        'url' => 'poster2',
                    ],
                    $typesInsertPoster
                ],
                [
                    $sqlReplacePublication,
                    [
                        'externalId' => 'externId2',
                        'title' => 'title2',
                        'year' => 2002,
                        'type' => 'type2',
                        'poster_id' => 2
                    ],
                    $typesReplacePublication
                ],
                [
                    $sqlReplacePublication,
                    [
                        'externalId' => 'externId3',
                        'title' => 'title3',
                        'year' => 2003,
                        'type' => 'type3',
                        'poster_id' => 1
                    ],
                    $typesReplacePublication
                ]
            );

        // WHEN
        $this->storage->save($publications);
    }

    protected function mockDbConnection(): mixed
    {
        return self::getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
