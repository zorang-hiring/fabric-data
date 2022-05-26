<?php
declare(strict_types=1);

namespace App\api\Repository;

use App\api\Model\PublicationModelCollection;

interface PublicationRepository
{
    public function save(PublicationRepositorySaveDto $publication): void;

    public function searchByTitle(string $filterByTitle): PublicationModelCollection;
}