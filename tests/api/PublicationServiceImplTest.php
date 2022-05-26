<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\PublicationDto;
use App\api\PublicationDtoCollection;
use App\api\PublicationDtoFactory;
use App\api\PublicationServiceImpl;
use PHPUnit\Framework\TestCase;

class PublicationServiceImplTest extends TestCase
{
    const TITLE_SEARCH = 'some title';

    protected PublicationExternGatewayFake $externGateway;
    protected PublicationLocalStorageSpy $localStorage;
    protected PublicationServiceImpl $service;
    protected PublicationDtoFactory $pblFactory;

    protected PublicationDto $publicationA;
    protected PublicationDto $publicationB;
    protected PublicationDto $publicationC;
    protected PublicationDto $publicationD;

    protected function setUp(): void
    {
        parent::setUp();
        $this->externGateway = new PublicationExternGatewayFake();
        $this->localStorage = new PublicationLocalStorageSpy();
        $this->pblFactory = new PublicationDtoFactory();
        $this->service = new PublicationServiceImpl(
            $this->externGateway,
            $this->localStorage
        );

        $this->publicationA = $this->pblFactory->makeOne([
            'externalId' => 'ImdbID1',
            'type' => 'movie',
            'title' => 'some title 1',
            'year' => 2001,
            'poster' => 'poster1',
        ]);
        $this->publicationB = $this->pblFactory->makeOne([
            'externalId' => 'ImdbID2',
            'type' => 'game',
            'title' => 'some title 2',
            'year' => 2002,
            'poster' => 'poster2',
        ]);
        $this->publicationC = $this->pblFactory->makeOne([
            'externalId' => 'ImdbID3',
            'type' => 'movie',
            'title' => 'some title 3',
            'year' => 2003,
            'poster' => 'poster3',
        ]);
        $this->publicationD = $this->pblFactory->makeOne([
            'externalId' => 'ImdbID4',
            'type' => 'movie',
            'title' => 'some title 4',
            'year' => 2004,
            'poster' => 'poster4',
        ]);
    }
    
    public function test_when_fetch_empty_store_and_has_nothing_locally_then_store_nothing_and_return_empty()
    {
        // GIVEN
        $this->externGateway->setFakeResult(self::TITLE_SEARCH, new PublicationDtoCollection());

        // WHEN,
        $result = $this->service->handle(self::TITLE_SEARCH);

        // THEN
        self::assertEquals(new PublicationDtoCollection(), $result);
        self::assertEquals(
            null,
            $this->localStorage->spySavedItems()
        );
    }

    public function test_when_fetch_collectionA_and_has_nothing_locally_then_store_whole_collectionA_and_return_it()
    {
        // GIVEN
        $this->externGateway->setFakeResult(
            self::TITLE_SEARCH,
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB)
        );
        $this->localStorage->setFakeFindByTitle(
            self::TITLE_SEARCH,
            new PublicationDtoCollection()
        );

        // WHEN
        $result = $this->service->handle(self::TITLE_SEARCH);

        // THEN
        self::assertEquals(
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB),
            $result
        );
        self::assertEquals(
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB),
            $this->localStorage->spySavedItems()
        );
    }

    public function test_when_fetch_collectionA_and_has_the_same_locally_then_do_not_store_but_return_it()
    {
        // GIVEN
        $this->externGateway->setFakeResult(
            self::TITLE_SEARCH,
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB)
        );
        $this->localStorage->setFakeFindByTitle(
            self::TITLE_SEARCH,
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB)
        );

        // WHEN
        $result = $this->service->handle(self::TITLE_SEARCH);

        // THEN
        self::assertEquals(
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB),
            $result
        );
        self::assertSame(
            null,
            $this->localStorage->spySavedItems()
        );
    }

    public function test_when_fetch_collectionA_and_has_other_items_stored_for_same_title_then_store_new_and_return_all()
    {
        // GIVEN
        $this->externGateway->setFakeResult(
            'some title',
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB)
                ->addItem($this->publicationC)
        );
        $this->localStorage->setFakeFindByTitle(
            self::TITLE_SEARCH,
            (new PublicationDtoCollection())
                ->addItem($this->publicationB)
                ->addItem($this->publicationD)
        );

        // WHEN
        $result = $this->service->handle(self::TITLE_SEARCH);

        // THEN
        self::assertEquals(
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationB)
                ->addItem($this->publicationC)
                ->addItem($this->publicationD),
            $result
        );
        self::assertEquals(
            (new PublicationDtoCollection())
                ->addItem($this->publicationA)
                ->addItem($this->publicationC),
            $this->localStorage->spySavedItems()
        );
    }
}