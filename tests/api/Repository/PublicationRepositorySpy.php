<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use App\api\Model\PublicationModelCollection;
use App\api\Repository\PublicationRepository;
use App\api\Repository\PublicationRepositorySaveDto;

class PublicationRepositorySpy implements PublicationRepository
{
    protected array $searchByTitle = [];

    protected array $savedPublications = [];

    public function save(PublicationRepositorySaveDto $publication): void
    {
        $this->savedPublications[] = $publication;
    }

    /**
     * @return PublicationRepositorySaveDto[]
     */
    public function getSavedPublications(): array
    {
        return $this->savedPublications;
    }

    public function searchByTitle(string $filterByTitle): PublicationModelCollection
    {
        if (array_key_exists($filterByTitle, $this->searchByTitle)) {
            return $this->searchByTitle[$filterByTitle];
        }
        throw new \InvalidArgumentException(sprintf('no result for "%s" title', $filterByTitle));
    }

    public function setSearchByTitle(array $options)
    {
        $this->searchByTitle[$options['forTitle']] = $options['willReturn'];
    }
}