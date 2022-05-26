<?php
declare(strict_types=1);

namespace App\api\Model;

class PublicationModel implements \JsonSerializable
{
    public string $externalId;
    public string $type;
    public string $tittle;
    public int $year;
    public ?string $poster;

    public function isEqual(PublicationModel $publication)
    {
        if (
            $this->externalId == $publication->externalId
            && $this->type == $publication->type
            && $this->tittle == $publication->tittle
            && $this->year == $publication->year
            && $this->poster == $publication->poster
        ) {
            return true;
        }
        return false;
    }

    public function jsonSerialize()
    {
        return [
            'externalId' => $this->externalId,
            'type' => $this->type,
            'title' => $this->tittle,
            'year' => $this->year ?? null,
            'poster' => $this->poster ?? null,
        ];
    }
}