<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\PublicationDtoCollection;
use App\api\PublicationExternGateway;

class PublicationExternGatewayFake implements PublicationExternGateway
{
    protected array $fakeResults = [];

    public function setFakeResult($forTitle, PublicationDtoCollection $willReturn)
    {
        $this->fakeResults[$forTitle] = $willReturn;
    }

    public function search(string $title): PublicationDtoCollection
    {
        if (array_key_exists($title, $this->fakeResults)) {
            return $this->fakeResults[$title];
        }
        throw new \InvalidArgumentException(sprintf(
            'Fake results not defined for "%s" title',
            $title)
        );
    }
}