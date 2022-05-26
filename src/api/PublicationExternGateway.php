<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationDtoCollection;

interface PublicationExternGateway
{
    /**
     * @param string $title
     * @return PublicationDtoCollection
     * @throws UnexpectedExternalPublicationsException
     */
    public function search(string $title) : PublicationDtoCollection;
}