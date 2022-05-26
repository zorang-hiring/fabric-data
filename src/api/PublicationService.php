<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationDtoCollection;

interface PublicationService
{
    public function handle(string $filterByTitle): PublicationDtoCollection;
}