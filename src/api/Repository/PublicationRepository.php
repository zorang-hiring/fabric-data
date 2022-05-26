<?php
declare(strict_types=1);

namespace App\api\Repository;

use App\api\Model\PublicationModelCollection;

interface PublicationRepository
{
    public function save(PublicationModelCollection $publications): void;

    public function searchByTitle(string $filterByTitle): PublicationModelCollection;
}