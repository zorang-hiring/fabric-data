<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelCollection;

interface PublicationInternalStore
{
    public function storeNewPublications(PublicationModelCollection $publications): void;

    public function searchByTitle(string $filterByTitle): PublicationModelCollection;
}