<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\PublicationDto;
use App\api\PublicationDtoCollection;
use PHPUnit\Framework\TestCase;

class PublicationDtoCollectionTest extends TestCase
{
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
}
