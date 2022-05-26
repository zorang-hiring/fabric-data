<?php
declare(strict_types=1);

namespace App\Tests\api;

use App\api\Model\PublicationDtoCollection;
use App\api\PublicationLocalStorage;

class PublicationLocalStorageSpy implements PublicationLocalStorage
{
    protected PublicationDtoCollection $spyStored;
    protected array $fakeFindByTitle = [];

    /**
     * @return PublicationDtoCollection|null If null then nothing is stored
     */
    public function spySavedItems(): ?PublicationDtoCollection
    {
        if (isset($this->spyStored)) {
            return $this->spyStored;
        }
        return null;
    }

    public function save(PublicationDtoCollection $publications): void
    {
        $this->spyStored = $publications;
    }

    public function setFakeFindByTitle(string $title, PublicationDtoCollection $publications): void
    {
        $this->fakeFindByTitle[$title] = $publications;
    }

    public function searchByTitle(string $filterByTitle): PublicationDtoCollection
    {
        if (array_key_exists($filterByTitle, $this->fakeFindByTitle)) {
            return $this->fakeFindByTitle[$filterByTitle];
        }
        return new PublicationDtoCollection();
    }
}