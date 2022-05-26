<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationDtoCollection;

interface PublicationLocalStorage
{
    public function save(PublicationDtoCollection $publications): void;

    public function searchByTitle(string $filterByTitle): PublicationDtoCollection;
}