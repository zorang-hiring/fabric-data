<?php
declare(strict_types=1);

namespace App\api;

use App\api\Model\PublicationDtoFactory;
use Doctrine\DBAL\DriverManager;
use GuzzleHttp\Client;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

// init Server
$server = AppFactory::create();

// init Controller
$controller = new PublicationController(
    new PublicationServiceImpl(
        new PublicationExternGatewayImpl(
            new Client(),
            (string) $_ENV['APP_3RD_PARTY_PUBLICATIONS_API_KEY']
        ),
        new PublicationLocalStorageImpl(
            DriverManager::getConnection([
                'password' => (string) $_ENV['MYSQL_PASSWORD'],
                'user' => (string) $_ENV['MYSQL_USER'],
                'host' => (string) $_ENV['MYSQL_ALIAS'],
                'dbname' => (string) $_ENV['MYSQL_DATABASE'],
                'driver' => 'pdo_mysql'
            ]),
            new PublicationDtoFactory()
        ),
    )
);

// configure route
$server->get('/api/publications', $controller);

// default route
$server->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$server->run();