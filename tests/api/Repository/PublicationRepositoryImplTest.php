<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\Repository\PublicationRepositoryImpl;
use App\api\Repository\PublicationRepositorySaveDto;
use Doctrine\DBAL\ParameterType;

class PublicationRepositoryImplTest extends AbstractRepositoryTestCase
{
    protected PublicationRepositoryImpl $storage;
    protected PublicationModelFactory $modelFactory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new PublicationModelFactory();
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
                . "LEFT JOIN posters po ON pu.poster_id = po.id "
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
        $publications = [];

        $dto = new PublicationRepositorySaveDto();
        $dto->externalId = 'externalId1';
        $dto->type = 'type1';
        $dto->title = 'title1';
        $dto->year = 2001;
        $dto->posterId = null;
        $publications[] = $dto;

        $dto = new PublicationRepositorySaveDto();
        $dto->externalId = 'externalId2';
        $dto->type = 'type2';
        $dto->title = 'title2';
        $dto->year = 2002;
        $dto->posterId = 2;
        $publications[] = $dto;


        // EXPECTED
        $sqlReplacePublication =
            "REPLACE INTO publications (externalId, title, year, type, poster_id) values "
            . "(:externalId, :title, :year, :type, :poster_id)";

        $typesReplacePublication = [
            'externalId' => ParameterType::STRING,
            'title' => ParameterType::STRING,
            'year' => ParameterType::STRING,
            'type' => ParameterType::STRING,
            'poster_id' => ParameterType::INTEGER
        ];

        $this->connection
            ->expects(self::exactly(2))
            ->method('executeStatement')
            ->withConsecutive(
                [
                    $sqlReplacePublication,
                    [
                        'externalId' => 'externalId1',
                        'title' => 'title1',
                        'year' => 2001,
                        'type' => 'type1',
                        'poster_id' => null
                    ],
                    $typesReplacePublication
                ],
                [
                    $sqlReplacePublication,
                    [
                        'externalId' => 'externalId2',
                        'title' => 'title2',
                        'year' => 2002,
                        'type' => 'type2',
                        'poster_id' => 2
                    ],
                    $typesReplacePublication
                ]
            );

        // WHEN
        $this->storage->save($publications[0]);
        $this->storage->save($publications[1]);
    }
}
