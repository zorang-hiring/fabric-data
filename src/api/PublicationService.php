<?php
declare(strict_types=1);

namespace App\api;

interface PublicationService
{
    public function handle(string $filterByTitle): PublicationDtoCollection;
}