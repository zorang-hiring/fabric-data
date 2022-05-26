<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationDtoCollection;
use App\api\PublicationExternGateway;

class PublicationExternGatewayFake implements PublicationExternGateway
{
    protected array $fakeResults = [];

    public function setFakeResult($forTitle, PublicationDtoCollection|UnexpectedExternalPublicationsException $willReturn)
    {
        $this->fakeResults[$forTitle] = $willReturn;
    }

    public function search(string $title): PublicationDtoCollection
    {
        if (array_key_exists($title, $this->fakeResults)) {
            $result = $this->fakeResults[$title];
            if ($result instanceof \Exception) {
                throw $result;
            }
            return $result;
        }
        throw new \InvalidArgumentException(sprintf(
            'Fake results not defined for "%s" title',
            $title)
        );
    }
}