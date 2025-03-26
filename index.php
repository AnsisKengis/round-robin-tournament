<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\TournamentController;
use App\Config\DatabaseConfig;

// Get EntityManager from configuration
$entityManager = DatabaseConfig::getEntityManager();

// Simple router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Initialize controller with EntityManager
$tournamentController = new TournamentController($entityManager);

try {
    switch ($path) {
        case '/':
            include __DIR__ . '/public/index.html';
            break;

        case '/tournament/create':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                echo $tournamentController->create($data);
            }
            break;

        case '/tournament/results':
            if ($method === 'GET') {
                // Retrieve and validate the id from the query string
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

                // Check if the id is valid (filter_input returns false or null if invalid)
                if ($id === false || $id === null) {
                    echo "Invalid tournament id.";
                    exit;
                }

                echo $tournamentController->results($id);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => '404 Not Found']);
            break;
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
