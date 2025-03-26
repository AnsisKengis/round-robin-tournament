<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\TournamentController;
use App\Config\DatabaseConfig;

// Get EntityManager from configuration
$entityManager = DatabaseConfig::getEntityManager();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$tournamentController = new TournamentController($entityManager);

try {
    // Match tournament results with ID in URL
    if (preg_match('#^/tournament/(\d+)$#', $path, $matches)) {
        $id = (int)$matches[1];
        // If we create tournament and it's an AJAX request, return JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo $tournamentController->results($id);
        } else {
            // For direct URL access, serve the HTML page with tournament ID
            $tournamentId = $id;
            include __DIR__ . '/public/index.html';
        }
        exit;
    }

    switch ($path) {
        case '/':
            $tournamentId = null;
            include __DIR__ . '/public/index.html';
            break;

        case '/tournament/create':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                echo $tournamentController->create($data);
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
