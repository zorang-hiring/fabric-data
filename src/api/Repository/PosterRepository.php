<?php
declare(strict_types=1);

namespace App\api\Repository;

use App\api\Exception\PosterRepositoryInsertException;

interface PosterRepository
{
    public function findPosterId(string $url): ?int;

    /**
     * @param string $url
     * @return int Returns poster id
     * @throws PosterRepositoryInsertException
     */
    public function insertPoster(string $url): int;
}