<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\PublicationLocalStorageImpl;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Connection;

class PublicationLocalStorageImplTest extends TestCase
{
    protected PublicationLocalStorageImpl $storage;
    protected Connection|MockObject $connection;
    protected PublicationModelFactory $modelFactory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new PublicationModelFactory();
        $this->connection = $this->mockDbConnection();
        $this->storage = new PublicationLocalStorageImpl(
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
                'poster' => 'poster1',
            ]))
            ->addItem($this->modelFactory->makeOne([
                'externalId' => 'externId2',
                'type' => 'type2',
                'title' => 'title2',
                'year' => '2002',
                'poster' => 'poster2'
            ]));

        // EXPECTED
        $dataTypes = [
            'externalId' => ParameterType::STRING,
            'title' => ParameterType::STRING,
            'year' => ParameterType::STRING,
            'type' => ParameterType::STRING,
            'poster' => ParameterType::STRING
        ];
        $sql = "REPLACE into publications (externalId, title, year, type, poster) values "
            . "(:externalId, :title, :year, :type, :poster)";
        $this->connection
            ->expects(self::exactly(2))
            ->method('executeStatement')
            ->withConsecutive(
                [
                    $sql,
                    [
                        'externalId' => 'externId1',
                        'title' => 'title1',
                        'year' => '2001',
                        'type' => 'type1',
                        'poster' => 'poster1'
                    ],
                    $dataTypes
                ],
                [
                    $sql,
                    [
                        'externalId' => 'externId2',
                        'title' => 'title2',
                        'year' => '2002',
                        'type' => 'type2',
                        'poster' => 'poster2'
                    ],
                    $dataTypes
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
