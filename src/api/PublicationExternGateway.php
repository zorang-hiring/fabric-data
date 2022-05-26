<?php
declare(strict_types=1);

namespace App\api;

interface PublicationExternGateway
{
    public function search(string $title) : PublicationDtoCollection;
}