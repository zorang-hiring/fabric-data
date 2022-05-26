<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelCollection;

interface PublicationLocalStorage
{
    public function save(PublicationModelCollection $publications): void;

    public function searchByTitle(string $filterByTitle): PublicationModelCollection;
}