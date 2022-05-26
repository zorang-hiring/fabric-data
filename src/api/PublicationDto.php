<?php
declare(strict_types=1);

namespace App\api;

// {
//"Title":"The Matrix Reloaded",
//"Year":"2003",
//"imdbID":"tt0234215",
//"Type":"movie",
//"Poster":"https://m.media-amazon.com/images/M/MV5BODE0MzZhZTgtYzkwYi00YmI5LThlZWYtOWRmNWE5ODk0NzMxXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg"
//}

class PublicationDto
{
    public string $externalId;
    public string $type;
    public string $tittle;
    public int $year;
    public string $poster;

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
}