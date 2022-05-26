<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelCollection;

interface PublicationService
{
    public function handle(string $filterByTitle): PublicationModelCollection;
}