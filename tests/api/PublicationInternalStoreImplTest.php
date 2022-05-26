<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use App\api\PublicationInternalStoreImpl;
use App\Tests\api\Repository\PosterRepositorySpy;
use App\Tests\api\Repository\PublicationRepositorySpy;
use PHPUnit\Framework\TestCase;

class PublicationInternalStoreImplTest extends TestCase
{
    protected PublicationInternalStoreImpl $store;
    protected PublicationRepositorySpy $publicationRepo;
    protected PosterRepositorySpy $posterRepo;
    protected PublicationModelFactory $modelFactory;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modelFactory = new PublicationModelFactory();
        $this->publicationRepo = new PublicationRepositorySpy();
        $this->posterRepo = new PosterRepositorySpy();
        $this->store = new PublicationInternalStoreImpl(
            $this->publicationRepo,
            $this->posterRepo
        );
    }

//    public function testStore()
//    {
//        // GIVEN
//        $publications = new PublicationModelCollection();
//        $publications->addItem($this->modelFactory->makeOne([
//            'externalId' => 'externId1',
//            'type' => 'type1',
//            'title' => 'title1',
//            'year' => 2001,
//            'poster' => 'poster13',
//        ]));
//        $publications->addItem($this->modelFactory->makeOne([
//            'externalId' => 'externId2',
//            'type' => 'type2',
//            'title' => 'title2',
//            'year' => 2002,
//            'poster' => 'poster2',
//        ]));
//        $publications->addItem($this->modelFactory->makeOne([
//            'externalId' => 'externId3',
//            'type' => 'type3',
//            'title' => 'title3',
//            'year' => 2003,
//            'poster' => 'poster13',
//        ]));
//        $publications->addItem($this->modelFactory->makeOne([
//            'externalId' => 'externId4',
//            'type' => 'type4',
//            'title' => 'title4',
//            'year' => 2004,
//            'poster' => null,
//        ]));
//
//        // WHEN
//        $this->store->store($publications);
//
//        // THEN
//    }

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
}
