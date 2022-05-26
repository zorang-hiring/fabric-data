<?php
declare(strict_types=1);

namespace App\api\Model;

class PublicationDto implements \JsonSerializable
{
    public string $externalId;
    public string $type;
    public string $tittle;
    public int $year;
    public ?string $poster;

    public function isEqual(PublicationDto $publication)
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
            'year' => $this->year,
            'poster' => $this->poster ?? null,
        ];
    }
}