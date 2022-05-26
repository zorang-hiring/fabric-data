<?php
declare(strict_types=1);

namespace App\Tests\api\Repository;

use App\api\Model\PublicationModelCollection;
use App\api\Repository\PublicationRepository;

class PublicationRepositorySpy implements PublicationRepository
{
    protected PublicationModelCollection $spyStored;
    protected array $fakeFindByTitle = [];

    /**
     * @return PublicationModelCollection|null If null then nothing is stored
     */
    public function spySavedItems(): ?PublicationModelCollection
    {
        if (isset($this->spyStored)) {
            return $this->spyStored;
        }
        return null;
    }

    public function save(PublicationModelCollection $publications): void
    {
        $this->spyStored = $publications;
    }

    public function setFakeFindByTitle(string $title, PublicationModelCollection $publications): void
    {
        $this->fakeFindByTitle[$title] = $publications;
    }

    public function searchByTitle(string $filterByTitle): PublicationModelCollection
    {
        if (array_key_exists($filterByTitle, $this->fakeFindByTitle)) {
            return $this->fakeFindByTitle[$filterByTitle];
        }
        return new PublicationModelCollection();
    }
}