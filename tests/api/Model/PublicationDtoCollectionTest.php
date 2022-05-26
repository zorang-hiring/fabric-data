<?php
declare(strict_types=1);

namespace App\Tests\api\Model;

use App\api\Model\PublicationDto;
use App\api\Model\PublicationDtoCollection;
use App\api\Model\PublicationDtoFactory;
use PHPUnit\Framework\TestCase;

class PublicationDtoCollectionTest extends TestCase
{
    protected PublicationDtoFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new PublicationDtoFactory();
    }

    public function testAll()
    {
        // GIVEN
        $collection = new PublicationDtoCollection();
        $collection->addItem($item1 = new PublicationDto());
        $collection->addItem($item2 = new PublicationDto());

        // WHEN, THEN
        self::assertSame(2, count($collection));
        self::assertSame([$item1, $item2], $collection->getAll());
    }

    public function testHasEqual()
    {
        $collection = new PublicationDtoCollection();
        $collection->addItem($this->factory->makeOne([
            'externalId' => 'ImdbID',
            'type' => 'movie',
            'title' => 'some title',
            'year' => 2001,
            'poster' => 'poster',
        ]));

        self::assertTrue(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => 'ImdbID',
                'type' => 'movie',
                'title' => 'some title',
                'year' => 2001,
                'poster' => 'poster',
            ]))
        );
        self::assertFalse(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => '?',
                'type' => 'movie',
                'title' => 'some title',
                'year' => 2001,
                'poster' => 'poster',
            ]))
        );
        self::assertFalse(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => 'ImdbID',
                'type' => '?',
                'title' => 'some title',
                'year' => 2001,
                'poster' => 'poster',
            ]))
        );
        self::assertFalse(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => 'ImdbID',
                'type' => 'movie',
                'title' => '?',
                'year' => 2001,
                'poster' => 'poster',
            ]))
        );
        self::assertFalse(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => 'ImdbID',
                'type' => 'movie',
                'title' => 'some title',
                'year' => 0,
                'poster' => 'poster',
            ]))
        );
        self::assertFalse(
            $collection->hasEqual($this->factory->makeOne([
                'externalId' => 'ImdbID',
                'type' => 'movie',
                'title' => 'some title',
                'year' => 2001,
                'poster' => '?',
            ]))
        );
    }
}
