<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelCollection;
use App\api\Repository\PosterRepository;
use App\api\Repository\PublicationRepository;
use App\api\Repository\PublicationRepositorySaveDto;

class PublicationInternalStoreImpl implements PublicationInternalStore
{
    public function __construct(
        protected PublicationRepository $publicationRepo,
        protected PosterRepository $posterRepo,
    ){}

    public function storeNewPublications(PublicationModelCollection $publications): void
    {
        foreach ($publications->getAll() as $publication) {

            $posterId = null;
            if (
                $publication->poster
                && !$posterId = $this->posterRepo->findPosterId($publication->poster)
            ) {
                $posterId = $this->posterRepo->insertPoster($publication->poster);
            }

            $this->publicationRepo->save($this->makePublication([
                'externalId' => $publication->externalId,
                'type' => $publication->type,
                'title' => $publication->tittle,
                'year' => $publication->year,
                'posterId' => $posterId,
            ]));
        }
    }

    public function searchByTitle(string $filterByTitle): PublicationModelCollection
    {
        return $this->publicationRepo->searchByTitle($filterByTitle);
    }

    private function makePublication(array $data): PublicationRepositorySaveDto
    {
        $dto = new PublicationRepositorySaveDto();
        $dto->externalId = $data['externalId'];
        $dto->type = $data['type'];
        $dto->title = $data['title'];
        $dto->year = $data['year'];
        $dto->posterId = $data['posterId'];
        return $dto;
    }
}