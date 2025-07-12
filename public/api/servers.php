<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\ServerController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$controller = new ServerController();

try {
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':
			$controller->getServers();
			break;
			
		case 'POST':
			$action = $_POST['action'] ?? $_GET['action'] ?? null;
			
			switch ($action) {
				case 'add':
					$controller->addServer();
					break;
					
				case 'update_password':
					$controller->updateRconPassword();
					break;
					
				case 'delete':
					$controller->deleteServer();
					break;
					
				default:
					$controller->addServer();
					break;
			}
			break;
			
		default:
			http_response_code(405);
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Method not allowed']);
			break;
	}
} catch (Exception $e) {
	error_log('API Error: ' . $e->getMessage());
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode(['error' => 'Internal server error']);
}
