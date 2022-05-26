<?php
declare(strict_types=1);

namespace App\api\Repository;

use App\api\Exception\PosterRepositoryInsertException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

class PosterRepositoryImpl implements PosterRepository
{
    public function __construct(
        protected Connection $connection
    ){}

    public function findPosterId(string $url): ?int
    {
        $result = $this->connection->fetchOne(
            'SELECT id FROM posters WHERE md5 = :md5',
            ['md5' => md5($url)],
            ['md5' => ParameterType::STRING]
        );

        return $result['id'] ?? null;
    }

    public function insertPoster(string $url): int
    {
        $this->connection->executeStatement(
            "INSERT INTO posters (md5, url) values (:md5, :url)",
            [
                'md5' => md5($url),
                'url' => $url,
            ],
            [
                'md5' => ParameterType::STRING,
                'url' => ParameterType::STRING,
            ]
        );

        if (!$id = $this->connection->lastInsertId()) {
            throw new PosterRepositoryInsertException();
        }

        return $id;
    }
}