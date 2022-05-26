<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\PublicationInternalStoreImpl;
use App\api\Repository\PosterRepository;
use App\api\Repository\PublicationRepositorySaveDto;
use App\Tests\api\Repository\PublicationRepositorySpy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PublicationInternalStoreImplTest extends TestCase
{
    protected PublicationInternalStoreImpl $store;
    protected PublicationRepositorySpy $publicationRepo;
    protected PosterRepository|MockObject $posterRepo;
    protected PublicationModelFactory $modelFactory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new PublicationModelFactory();
        $this->publicationRepo = new PublicationRepositorySpy();
        $this->posterRepo = $this->mockPosterRepo();
        $this->store = new PublicationInternalStoreImpl(
            $this->publicationRepo,
            $this->posterRepo
        );
    }

    public function testStoreNewPublications()
    {
        // GIVEN
        $publications = new PublicationModelCollection();
        $publications->addItem($this->modelFactory->makeOne([
            'externalId' => 'externId1',
            'type' => 'type1',
            'title' => 'title1',
            'year' => 2001,
            'poster' => 'poster13',
        ]));
        $publications->addItem($this->modelFactory->makeOne([
            'externalId' => 'externId2',
            'type' => 'type2',
            'title' => 'title2',
            'year' => 2002,
            'poster' => 'poster2',
        ]));
        $publications->addItem($this->modelFactory->makeOne([
            'externalId' => 'externId3',
            'type' => 'type3',
            'title' => 'title3',
            'year' => 2003,
            'poster' => 'poster13',
        ]));
        $publications->addItem($this->modelFactory->makeOne([
            'externalId' => 'externId4',
            'type' => 'type4',
            'title' => 'title4',
            'year' => 2004,
            'poster' => null,
        ]));

        // EXPECTS
        $this->posterRepo->expects(self::exactly(3))
            ->method('findPosterId')
            ->withConsecutive(['poster13'], ['poster2'], ['poster13'])
            ->willReturnOnConsecutiveCalls(null, null, 1013);

        $this->posterRepo->expects(self::exactly(2))
            ->method('insertPoster')
            ->withConsecutive(['poster13'], ['poster2'])
            ->willReturnOnConsecutiveCalls(1013, 102);

        // WHEN
        $this->store->storeNewPublications($publications);

        // THEN
        self::assertEquals(
            [
                $this->makePublication([
                    'externalId' => 'externId1',
                    'type' => 'type1',
                    'title' => 'title1',
                    'year' => 2001,
                    'posterId' => 1013,
                ]),
                $this->makePublication([
                    'externalId' => 'externId2',
                    'type' => 'type2',
                    'title' => 'title2',
                    'year' => 2002,
                    'posterId' => 102,
                ]),
                $this->makePublication([
                    'externalId' => 'externId3',
                    'type' => 'type3',
                    'title' => 'title3',
                    'year' => 2003,
                    'posterId' => 1013,
                ]),
                $this->makePublication([
                    'externalId' => 'externId4',
                    'type' => 'type4',
                    'title' => 'title4',
                    'year' => 2004,
                    'posterId' => null,
                ])
            ],
            $this->publicationRepo->getSavedPublications()
        );
    }

    public function testSearchByTitle()
    {
        $this->publicationRepo->setSearchByTitle([
            'forTitle' => 'someTitle',
            'willReturn' => $expectedResult = new PublicationModelCollection()
        ]);

        self::assertSame(
            $expectedResult,
            $this->store->searchByTitle('someTitle')
        );
    }

    protected function mockPosterRepo(): mixed
    {
        return self::getMockBuilder(PosterRepository::class)
            ->getMock();
    }

    private function makePublication(array $data): PublicationRepositorySaveDto
    {
        $dto = new PublicationRepositorySaveDto();
        $dto->externalId = $data['externalId'];
        $dto->type = $data['type'];
        $dto->title = $data['title'];
        $dto->year = $data['year'];
        $dto->posterId = $data['posterId'];
        return $dto;
    }
}
