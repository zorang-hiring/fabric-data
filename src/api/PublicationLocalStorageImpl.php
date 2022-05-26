<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationDtoCollection;
use App\api\Model\PublicationDtoFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

class PublicationLocalStorageImpl implements PublicationLocalStorage
{
    public function __construct(
        protected Connection $connection,
        protected PublicationDtoFactory $factory
    ){}

    public function save(PublicationDtoCollection $publications): void
    {
        foreach ($publications->getAll() as $publication) {
            $this->connection->executeStatement(
                "REPLACE into publications (externalId, title, year, type, poster) values "
                . "(:externalId, :title, :year, :type, :poster)",
                [
                    'externalId' => $publication->externalId,
                    'title' => $publication->tittle,
                    'year' => $publication->year,
                    'type' => $publication->type,
                    'poster' => $publication->poster
                ],
                [
                    'externalId' => ParameterType::STRING,
                    'title' => ParameterType::STRING,
                    'year' => ParameterType::STRING,
                    'type' => ParameterType::STRING,
                    'poster' => ParameterType::STRING
                ]
            );
        }
    }

    public function searchByTitle(string $filterByTitle): PublicationDtoCollection
    {

        $result = $this->connection->fetchAllAssociative(
            "SELECT * FROM publications WHERE title LIKE '%:title%'",
            ['title' => $filterByTitle],
            ['title' => ParameterType::STRING]
        );

        $return = new PublicationDtoCollection();
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