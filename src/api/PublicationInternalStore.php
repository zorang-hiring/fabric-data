<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelCollection;

interface PublicationInternalStore
{
    public function store(PublicationModelCollection $publications): void;
}