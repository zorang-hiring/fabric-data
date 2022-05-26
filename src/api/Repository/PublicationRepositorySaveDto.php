<?php
declare(strict_types=1);

namespace App\api\Repository;

class PublicationRepositorySaveDto
{
    public string $externalId;
    public string $type;
    public string $title;
    public ?int $year;
    public ?int $posterId;
}