<?php
declare(strict_types=1);

namespace App\api\Model;

class PublicationDtoCollection implements \Countable
{
    /**
     * @var PublicationDto[]
     */
    protected array $publications = [];

    public function addItem(PublicationDto $publication): self
    {
        $this->publications[] = $publication;
        return $this;
    }

    public function count()
    {
        return count($this->publications);
    }

    public function getAll(): array
    {
        return $this->publications;
    }

    public function hasEqual(PublicationDto $publication)
    {
        foreach ($this->getAll() as $item) {
            if ($item->isEqual($publication)) {
                return true;
            }
        }
        return false;
    }
}