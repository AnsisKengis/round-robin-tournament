<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\TournamentController;

// Get the EntityManager directly from our configuration
$dependencyFactory = require __DIR__ . '/cli-config.php';
$entityManager = $dependencyFactory->getEntityManager();

// Simple router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$tournamentController = new TournamentController($entityManager);

try {
    switch ($path) {
        case '/':
            echo $tournamentController->index();
            break;

        case '/tournament/create':
            $tournamentController->create();
            break;

        case '/tournament/results':
            echo $tournamentController->results();
            break;

        default:
            http_response_code(404);
            echo '404 Not Found';
            break;
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo '500 Internal Server Error';
    error_log($e->getMessage());
}
