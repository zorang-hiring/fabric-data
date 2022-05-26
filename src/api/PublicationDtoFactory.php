<?php
declare(strict_types=1);

namespace App\api;

class PublicationDtoFactory
{
    public function makeOne(
        array $data
    ): PublicationDto {
        $p = new PublicationDto();
        if (array_key_exists('externalId', $data)) {
            $p->externalId = $data['externalId'];
        }
        if (array_key_exists('type', $data)) {
            $p->type = $data['type'];
        }
        if (array_key_exists('title', $data)) {
            $p->tittle = $data['title'];
        }
        if (array_key_exists('year', $data)) {
            $p->year = $data['year'];
        }
        if (array_key_exists('poster', $data)) {
            $p->poster = $data['poster'];
        }
        return $p;
    }
}