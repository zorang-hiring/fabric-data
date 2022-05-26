<?php
declare(strict_types=1);

namespace App\api\Repository;

use App\api\Model\PublicationModelCollection;
use App\api\Model\PublicationModelFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

class PublicationRepositoryImpl implements PublicationRepository
{
    public function __construct(
        protected Connection $connection,
        protected PublicationModelFactory $factory
    ){}

    public function save(PublicationModelCollection $publications): void
    {
        // todo use transaction
        foreach ($publications->getAll() as $publication) {

            $this->connection->executeStatement(
                "INSERT INTO posters (md5, url) values (:md5, :url)",
                [
                    'md5' => md5($publication->poster),
                    'url' => $publication->poster,
                ],
                [
                    'md5' => ParameterType::STRING,
                    'url' => ParameterType::STRING,
                ]
            );

            $posterId = $this->connection->lastInsertId();

            $this->connection->executeStatement(
                "REPLACE INTO publications (externalId, title, year, type, poster_id) values "
                . "(:externalId, :title, :year, :type, :poster_id)",
                [
                    'externalId' => $publication->externalId,
                    'title' => $publication->tittle,
                    'year' => $publication->year,
                    'type' => $publication->type,
                    'poster_id' => $posterId
                ],
                [
                    'externalId' => ParameterType::STRING,
                    'title' => ParameterType::STRING,
                    'year' => ParameterType::STRING,
                    'type' => ParameterType::STRING,
                    'poster_id' => ParameterType::INTEGER
                ]
            );
        }
    }

    public function searchByTitle(string $filterByTitle): PublicationModelCollection
    {
        $result = $this->connection->fetchAllAssociative(
            "SELECT pu.externalId, pu.title, pu.year, pu.type, po.url as poster "
            . "FROM publications pu "
            . "LEFT JOIN posters po ON pu.poster_id = po.id"
            . "WHERE pu.title LIKE '%:title%'",
            ['title' => $filterByTitle],
            ['title' => ParameterType::STRING]
        );

        $return = new PublicationModelCollection();
        foreach ($result as $row) {
            $return->addItem($this->factory->makeOne([
                'externalId' => $row['externalId'],
                'title' => $row['title'],
                'year' => $row['year'],
                'type' => $row['type'],
                'poster' => $row['poster'],
            ]));
        }
        return $return;
    }
}