<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationModelFactory;
use App\api\Repository\PosterRepositoryImpl;
use App\api\Repository\PublicationRepositoryImpl;
use Doctrine\DBAL\DriverManager;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

// init Server
$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(
    (bool) $_ENV['APP_SHOW_ERRORS'],
    true,
    true
);
// Allow wide Server Cors policy
$app->add(function(Request $request, RequestHandler $handler): ResponseInterface {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', ['Authorization', 'Accept'])
        ->withHeader('Access-Control-Allow-Methods', ['GET, POST, PUT, DELETE, PATCH, OPTIONS']);
});

// init DB connection
$dbConnection = DriverManager::getConnection([
    'password' => (string) $_ENV['MYSQL_PASSWORD'],
    'user' => (string) $_ENV['MYSQL_USER'],
    'host' => (string) $_ENV['MYSQL_ALIAS'],
    'dbname' => (string) $_ENV['MYSQL_DATABASE'],
    'driver' => 'pdo_mysql'
]);

// init Publication Service
$publicationsService = new PublicationServiceImpl(
    new PublicationExternGatewayImpl(
        new HttpClient(),
        (string) $_ENV['APP_3RD_PARTY_PUBLICATIONS_API_KEY']
    ),
    new PublicationInternalStoreImpl(
        new PublicationRepositoryImpl(
            $dbConnection,
            new PublicationModelFactory()
        ),
        new PosterRepositoryImpl($dbConnection)
    )
);

// configure route
$app->get('/api/publications', new PublicationController($publicationsService));

$app->run();