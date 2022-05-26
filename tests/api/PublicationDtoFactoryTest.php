<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\PublicationDto;
use App\api\PublicationDtoFactory;
use PHPUnit\Framework\TestCase;

class PublicationDtoFactoryTest extends TestCase
{

    public function testMakeOne()
    {
        $o = new PublicationDto();
        $o->externalId = '1';
        $o->type = '2';
        $o->tittle = '3';
        $o->year = 2004;
        $o->poster = '5';

        self::assertEquals(
            $o,
            (new PublicationDtoFactory())->makeOne([
                'externalId' => '1',
                'type' => '2',
                'title' => '3',
                'year' => '2004',
                'poster' => '5',
            ])
        );
    }
}
