<?php
declare(strict_types=1);

namespace App\api;

use App\api\Exception\UnexpectedExternalPublicationsException;
use App\api\Model\PublicationModelCollection;

interface PublicationExternGateway
{
    /**
     * @param string $title
     * @return PublicationModelCollection
     * @throws UnexpectedExternalPublicationsException
     */
    public function search(string $title) : PublicationModelCollection;
}