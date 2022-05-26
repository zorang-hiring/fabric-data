<?php
declare(strict_types=1);

namespace App\api\Model;

class PublicationModelCollection implements \Countable, \JsonSerializable
{
    /**
     * @var PublicationModel[]
     */
    protected array $publications = [];

    public function addItem(PublicationModel $publication): self
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

    public function hasEqual(PublicationModel $publication)
    {
        foreach ($this->getAll() as $item) {
            if ($item->isEqual($publication)) {
                return true;
            }
        }
        return false;
    }

    public function jsonSerialize()
    {
        return $this->getAll();
    }
}