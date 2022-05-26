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


interface PublicationExternGateway
{
    public function search(string $title) : PublicationDtoCollection;
}